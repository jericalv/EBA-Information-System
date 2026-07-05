@extends('cashier.layout')

@section('title', 'Record Payment')

@section('content')
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong style="font-size:16px;color:#111827;">Active Approved Concessionaires</strong>
        </div>
        <div class="card-body">
            @if ($concessionaires->isEmpty())
                <div class="empty-state">No active approved concessionaires are available.</div>
            @else
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Contract Period</th>
                            <th>Monthly Fee</th>
                            <th>Total Paid</th>
                            <th>Last Payment</th>
                            <th>Payments</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($concessionaires as $concessionaire)
                            @php
                                $latestApplication = $concessionaire->latestPartnershipApplication;
                                $contractStart = $latestApplication?->contract_period_start;
                                $contractEnd = $latestApplication?->contract_period_end;
                                $statusKey = $concessionaireStatuses[$concessionaire->id] ?? 'no_contract';
                                $statusLabel = match ($statusKey) {
                                    'paid' => 'Paid ✓',
                                    'due_soon' => 'Due on 1st',
                                    'overdue' => 'Overdue',
                                    default => '—',
                                };
                                $statusClass = match ($statusKey) {
                                    'paid' => 'status-badge-paid',
                                    'due_soon' => 'status-badge-due',
                                    'overdue' => 'status-badge-overdue',
                                    default => 'status-badge-none',
                                };
                                $hasPaidThisMonth = $statusKey === 'paid';
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ $concessionaire->initials() }}</div>
                                        <div>
                                            <div class="user-name">{{ $concessionaire->business_name ?: $concessionaire->name }}</div>
                                            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                            <div class="user-email">{{ $concessionaire->name }} · {{ $concessionaire->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($contractStart && $contractEnd)
                                        <span style="font-weight:700;color:#334155;">{{ $contractStart->format('M d, Y') }} → {{ $contractEnd->format('M d, Y') }}</span>
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if (! is_null($concessionaire->monthly_fee))
                                        <span style="font-weight:700;color:#334155;">PHP {{ number_format((float) $concessionaire->monthly_fee, 2) }}</span>
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                                <td style="font-weight:800;color:#0a5c2f;">₱{{ number_format((float) ($concessionaire->total_paid ?? 0), 2) }}</td>
                                <td>{{ $concessionaire->last_payment_date ? \Illuminate\Support\Carbon::parse($concessionaire->last_payment_date)->format('M d, Y') : '—' }}</td>
                                <td class="payments-cell">
                                    @if ($hasPaidThisMonth)
                                        <span class="paid-month-badge">Paid This Month</span>
                                    @else
                                        <button
                                            type="button"
                                            class="btn record-payment-btn"
                                            data-user-id="{{ $concessionaire->id }}"
                                            data-business-name="{{ e($concessionaire->business_name ?: $concessionaire->name) }}"
                                            data-concessionaire-name="{{ e($concessionaire->name) }}"
                                        >
                                            Record Payment
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <div class="row-action-links">
                                        <a
                                            href="{{ route('cashier.payments.concessionaire.history.view', $concessionaire->id) }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="btn btn-outline btn-xs"
                                        >
                                             View
                                        </a>
                                        <a
                                            href="{{ route('cashier.payments.concessionaire.history.pdf', $concessionaire->id) }}"
                                            class="btn btn-outline btn-xs"
                                        >
                                            📂 History
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="modal-backdrop" id="paymentModal">
        <div class="modal">
            <h3>Record Payment</h3>
            <div class="notice" id="paymentModalSubtitle"></div>
            <form method="POST" action="{{ route('cashier.payments.store') }}" id="paymentForm">
                @csrf
                <input type="hidden" name="concessionaire_id" id="paymentConcessionaireId">

                <div class="field">
                    <label>Concessionaire</label>
                    <input type="text" id="paymentConcessionaireName" readonly>
                </div>

                <div class="field">
                    <label for="payment_amount">Amount <span class="required">*</span></label>
                    <input id="payment_amount" name="amount" type="number" step="0.01" min="1" required>
                </div>

                <div class="field">
                    <label for="payment_date">Payment Date <span class="required">*</span></label>
                    <input id="payment_date" name="payment_date" type="date" required>
                </div>

                <div class="field">
                    <label>Payment Type</label>
                    <input type="text" value="Cash" readonly>
                </div>

                <div class="field">
                    <label for="or_number">OR Number</label>
                    <input id="or_number" name="or_number" type="text" maxlength="255" placeholder="e.g. 7919825 T">
                    <small style="display:block;margin-top:6px;color:#64748b;font-style:italic;">Enter the OR number from the physical AF No. 51-C receipt.</small>
                </div>

                <div class="field">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" maxlength="500" placeholder="Optional note up to 500 characters"></textarea>
                </div>

                <div id="paymentFeedback" class="modal-feedback"></div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-outline" id="closePaymentModalButton">Cancel</button>
                    <button type="submit" class="btn btn-green" id="submitPaymentButton">Save Payment</button>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="flash-modal-backdrop active" id="successFlashModal" role="dialog" aria-modal="true" aria-labelledby="successFlashTitle">
            <div class="flash-modal">
                <div class="flash-modal-head" id="successFlashTitle">Payment Recorded</div>
                <div class="flash-modal-body">{{ session('success') }}</div>
                <div class="flash-modal-actions">
                    <button type="button" class="btn btn-green" id="successFlashCloseButton">OK</button>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        (() => {
            const paymentModal = document.getElementById('paymentModal');
            const paymentForm = document.getElementById('paymentForm');
            const paymentDateInput = document.getElementById('payment_date');
            const submitPaymentButton = document.getElementById('submitPaymentButton');
            const paymentFeedback = document.getElementById('paymentFeedback');
            const paymentSubtitle = document.getElementById('paymentModalSubtitle');
            const paymentConcessionaireName = document.getElementById('paymentConcessionaireName');
            const paymentConcessionaireId = document.getElementById('paymentConcessionaireId');
            const successFlashModal = document.getElementById('successFlashModal');
            const successFlashCloseButton = document.getElementById('successFlashCloseButton');

            function closeSuccessFlashModal() {
                if (successFlashModal) {
                    successFlashModal.classList.remove('active');
                }
            }

            function setPaymentFeedback(message, type) {
                paymentFeedback.classList.remove('error', 'success');
                if (!message) {
                    paymentFeedback.textContent = '';
                    paymentFeedback.style.display = 'none';
                    return;
                }
                paymentFeedback.textContent = message;
                paymentFeedback.classList.add(type === 'success' ? 'success' : 'error');
                paymentFeedback.style.display = 'block';
            }

            document.querySelectorAll('.record-payment-btn').forEach((button) => {
                button.addEventListener('click', function () {
                    paymentForm.reset();
                    paymentConcessionaireId.value = this.dataset.userId;
                    paymentConcessionaireName.value = `${this.dataset.businessName} (${this.dataset.concessionaireName})`;
                    paymentSubtitle.textContent = `Recording payment for ${this.dataset.businessName}.`;
                    paymentDateInput.value = new Date().toISOString().slice(0, 10);
                    setPaymentFeedback('', 'error');
                    paymentModal.classList.add('active');
                });
            });

            document.getElementById('closePaymentModalButton').addEventListener('click', function () {
                paymentModal.classList.remove('active');
                paymentForm.reset();
                setPaymentFeedback('', 'error');
            });

            paymentModal.addEventListener('click', function (event) {
                if (event.target === paymentModal) {
                    paymentModal.classList.remove('active');
                    paymentForm.reset();
                    setPaymentFeedback('', 'error');
                }
            });

            paymentForm.addEventListener('submit', function () {
                submitPaymentButton.disabled = true;
                submitPaymentButton.textContent = 'Saving...';
            });

            if (successFlashCloseButton) {
                successFlashCloseButton.addEventListener('click', function () {
                    closeSuccessFlashModal();
                });
            }

            if (successFlashModal) {
                successFlashModal.addEventListener('click', function (event) {
                    if (event.target === successFlashModal) {
                        closeSuccessFlashModal();
                    }
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSuccessFlashModal();
                }
            });
        })();
    </script>
@endsection
