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

    <div class="page-head">
        <div>
            <span class="eyebrow">Collections</span>
            <h1 class="page-title">Record Payment</h1>
        </div>
        <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="panel-title">Active approved concessionaires</h2>
            <p class="panel-sub">Record this month&rsquo;s fee or review a concessionaire&rsquo;s payment record.</p>
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
                                    'paid' => 'Paid',
                                    'due_soon' => 'Due on 1st',
                                    'overdue' => 'Overdue',
                                    default => 'No contract',
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
                                            <div class="user-email">{{ $concessionaire->name }} &middot; {{ $concessionaire->email }}</div>
                                            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($contractStart && $contractEnd)
                                        <span class="table-num">{{ $contractStart->format('M d, Y') }} &rarr; {{ $contractEnd->format('M d, Y') }}</span>
                                    @else
                                        <span class="table-dim">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    @if (! is_null($concessionaire->monthly_fee))
                                        <span class="table-num">&#8369;{{ number_format((float) $concessionaire->monthly_fee, 2) }}</span>
                                    @else
                                        <span class="table-dim">&mdash;</span>
                                    @endif
                                </td>
                                <td><span class="table-num is-pine">&#8369;{{ number_format((float) ($concessionaire->total_paid ?? 0), 2) }}</span></td>
                                <td>
                                    @if ($concessionaire->last_payment_date)
                                        <span class="table-num">{{ \Illuminate\Support\Carbon::parse($concessionaire->last_payment_date)->format('M d, Y') }}</span>
                                    @else
                                        <span class="table-dim">&mdash;</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap;">
                                    @if ($hasPaidThisMonth)
                                        <span class="paid-month-badge">
                                            <svg style="width:13px;height:13px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Paid this month
                                        </span>
                                    @else
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm record-payment-btn"
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
                                            class="btn btn-secondary btn-xs"
                                        >
                                            View
                                        </a>
                                        <a
                                            href="{{ route('cashier.payments.concessionaire.history.pdf', $concessionaire->id) }}"
                                            class="btn btn-secondary btn-xs"
                                        >
                                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/>
                                            </svg>
                                            History
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
                    <small class="field-help">Enter the OR number from the physical AF No. 51-C receipt.</small>
                </div>

                <div class="field">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" maxlength="500" placeholder="Optional note up to 500 characters"></textarea>
                </div>

                <div id="paymentFeedback" class="modal-feedback"></div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" id="closePaymentModalButton">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitPaymentButton">Save Payment</button>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="flash-modal-backdrop active" id="successFlashModal" role="dialog" aria-modal="true" aria-labelledby="successFlashTitle">
            <div class="flash-modal">
                <div class="flash-modal-head" id="successFlashTitle">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Payment Recorded
                </div>
                <div class="flash-modal-body">{{ session('success') }}</div>
                <div class="flash-modal-actions">
                    <button type="button" class="btn btn-primary" id="successFlashCloseButton">OK</button>
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
