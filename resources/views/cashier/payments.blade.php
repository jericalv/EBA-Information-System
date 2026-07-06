@extends('cashier.layout')

@section('title', 'Record Payment')

@section('extra-css')
<style>
    /* ---------- Owed indicator ---------- */
    .owed-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border-radius: 5px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        background: #FBEAEA;
        color: #B3261E;
        white-space: nowrap;
    }
    .uptodate-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border-radius: 5px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        background: #E5F3EA;
        color: #14532D;
        white-space: nowrap;
    }

    /* ---------- Month checklist (modal) ---------- */
    .month-picker {
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #FAFCFA;
        padding: 8px;
        max-height: 280px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .month-group-label {
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--faint);
        padding: 8px 4px 2px;
    }
    .month-group-label:first-child { padding-top: 2px; }
    .month-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border: 1px solid var(--line);
        border-radius: 6px;
        background: #fff;
        transition: opacity 0.12s ease, border-color 0.12s ease;
    }
    .month-row.is-arrear { border-color: #F0D6D6; }
    .month-row.is-off { opacity: 0.5; }
    .month-row label {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--ink);
        cursor: pointer;
        min-width: 0;
    }
    .month-row input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--pine);
        flex-shrink: 0;
        cursor: pointer;
    }
    .month-row .month-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .month-tag {
        font-family: var(--font-mono);
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 2px 6px;
        border-radius: 4px;
        flex-shrink: 0;
    }
    .month-tag.arrear { background: #FBEAEA; color: #B3261E; }
    .month-tag.advance { background: #EAF1FB; color: #1E40AF; }
    .month-amount {
        width: 118px;
        padding: 7px 10px;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font-family: var(--font-mono);
        font-size: 13px;
        font-variant-numeric: tabular-nums;
        text-align: right;
        background: #fff;
        color: var(--ink);
        flex-shrink: 0;
    }
    .month-amount:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
    }
    .month-empty {
        padding: 18px 12px;
        text-align: center;
        color: var(--muted);
        font-size: 13px;
    }
    .payment-total {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid var(--line);
    }
    .payment-total .pt-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
    }
    .payment-total .pt-sub {
        font-size: 12px;
        font-weight: 500;
        color: var(--muted);
    }
    .payment-total .pt-value {
        font-family: var(--font-mono);
        font-size: 22px;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--pine);
        font-variant-numeric: tabular-nums;
    }
    .modal-wide { width: min(600px, 100%); }
</style>
@endsection

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
            <p class="panel-sub">Record the current fee, settle unpaid past months, or take advance payments.</p>
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
                            <th>Standing</th>
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
                                $plan = $paymentPlans[$concessionaire->id] ?? null;
                                $owedCount = (int) ($plan['owed_count'] ?? 0);
                                $monthlyFee = (float) ($plan['monthly_fee'] ?? ($concessionaire->monthly_fee ?? 0));
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
                                    @if ($owedCount > 0)
                                        <span class="owed-chip">
                                            {{ $owedCount }} {{ \Illuminate\Support\Str::plural('month', $owedCount) }} owed
                                        </span>
                                    @elseif ($hasPaidThisMonth)
                                        <span class="uptodate-chip">
                                            <svg style="width:12px;height:12px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Up to date
                                        </span>
                                    @else
                                        <span class="table-dim">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="row-action-links">
                                        @if ($monthlyFee > 0 && ! empty($plan['months']))
                                            <button
                                                type="button"
                                                class="btn btn-primary btn-sm record-payment-btn"
                                                data-user-id="{{ $concessionaire->id }}"
                                            >
                                                Record Payment
                                            </button>
                                        @endif
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
        <div class="modal modal-wide">
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
                    <label>Months to pay <span class="required">*</span></label>
                    <div class="month-picker" id="monthPicker"></div>
                    <small class="field-help">Tick every month this payment covers. Arrears and the current month are pre-selected; tick advance months to pay ahead.</small>
                </div>

                <div class="payment-total">
                    <div>
                        <div class="pt-label">Total to record</div>
                        <div class="pt-sub" id="paymentCountLabel">0 months selected</div>
                    </div>
                    <div class="pt-value" id="paymentTotal">&#8369;0.00</div>
                </div>

                <div class="field" style="margin-top:16px;">
                    <label for="payment_date">Date received <span class="required">*</span></label>
                    <input id="payment_date" name="payment_date" type="date" required>
                    <small class="field-help">When the cash was actually collected. Defaults to today.</small>
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
            const paymentPlans = @json($paymentPlans ?? []);

            const paymentModal = document.getElementById('paymentModal');
            const paymentForm = document.getElementById('paymentForm');
            const paymentDateInput = document.getElementById('payment_date');
            const submitPaymentButton = document.getElementById('submitPaymentButton');
            const paymentFeedback = document.getElementById('paymentFeedback');
            const paymentSubtitle = document.getElementById('paymentModalSubtitle');
            const paymentConcessionaireName = document.getElementById('paymentConcessionaireName');
            const paymentConcessionaireId = document.getElementById('paymentConcessionaireId');
            const monthPicker = document.getElementById('monthPicker');
            const paymentTotal = document.getElementById('paymentTotal');
            const paymentCountLabel = document.getElementById('paymentCountLabel');
            const successFlashModal = document.getElementById('successFlashModal');
            const successFlashCloseButton = document.getElementById('successFlashCloseButton');

            const peso = (value) => '₱' + Number(value || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

            const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (ch) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[ch]));

            const groupMeta = [
                { key: 'arrears', title: 'Unpaid past months', tag: 'arrear', tagText: 'Arrears', checked: true, arrear: true },
                { key: 'current', title: 'This month', tag: null, tagText: '', checked: true, arrear: false },
                { key: 'advance', title: 'Pay in advance', tag: 'advance', tagText: 'Advance', checked: false, arrear: false },
            ];

            function renderMonths(plan) {
                const fee = Number(plan.monthly_fee || 0).toFixed(2);
                const byGroup = { arrears: [], current: [], advance: [] };
                (plan.months || []).forEach((m) => {
                    if (byGroup[m.group]) byGroup[m.group].push(m);
                });

                let html = '';
                groupMeta.forEach((meta) => {
                    const list = byGroup[meta.key];
                    if (!list.length) return;
                    html += '<div class="month-group-label">' + meta.title + '</div>';
                    list.forEach((m) => {
                        html += '<div class="month-row' + (meta.arrear ? ' is-arrear' : '') + (meta.checked ? '' : ' is-off') + '">'
                            + '<label>'
                            + '<input type="checkbox" name="months[]" value="' + escapeHtml(m.month) + '"' + (meta.checked ? ' checked' : '') + '>'
                            + '<span class="month-name">' + escapeHtml(m.label) + '</span>'
                            + (meta.tag ? '<span class="month-tag ' + meta.tag + '">' + meta.tagText + '</span>' : '')
                            + '</label>'
                            + '<input type="number" class="month-amount" name="amounts[' + escapeHtml(m.month) + ']" step="0.01" min="1" value="' + fee + '" aria-label="Amount for ' + escapeHtml(m.label) + '">'
                            + '</div>';
                    });
                });

                if (!html) {
                    html = '<div class="month-empty">This concessionaire is fully paid up — no months are due right now.</div>';
                }
                return html;
            }

            function refreshTotal() {
                let total = 0;
                let count = 0;
                monthPicker.querySelectorAll('.month-row').forEach((row) => {
                    const checkbox = row.querySelector('input[type="checkbox"]');
                    const amount = row.querySelector('.month-amount');
                    if (checkbox && checkbox.checked) {
                        row.classList.remove('is-off');
                        count += 1;
                        total += parseFloat(amount && amount.value) || 0;
                    } else {
                        row.classList.add('is-off');
                    }
                });
                paymentTotal.textContent = peso(total);
                paymentCountLabel.textContent = count + ' ' + (count === 1 ? 'month' : 'months') + ' selected';
                submitPaymentButton.disabled = count === 0;
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

            function closePaymentModal() {
                paymentModal.classList.remove('active');
                paymentForm.reset();
                monthPicker.innerHTML = '';
                setPaymentFeedback('', 'error');
            }

            monthPicker.addEventListener('change', refreshTotal);
            monthPicker.addEventListener('input', (event) => {
                if (event.target.classList.contains('month-amount')) refreshTotal();
            });

            document.querySelectorAll('.record-payment-btn').forEach((button) => {
                button.addEventListener('click', function () {
                    const plan = paymentPlans[this.dataset.userId];
                    if (!plan) return;

                    paymentForm.reset();
                    paymentConcessionaireId.value = this.dataset.userId;
                    paymentConcessionaireName.value = `${plan.business} (${plan.name})`;

                    const owed = Number(plan.owed_count || 0);
                    let subtitle = `Recording payment for ${plan.business}.`;
                    if (owed > 0) {
                        subtitle += ` ${owed} ${owed === 1 ? 'month is' : 'months are'} overdue.`;
                    }
                    paymentSubtitle.textContent = subtitle;

                    monthPicker.innerHTML = renderMonths(plan);
                    paymentDateInput.value = new Date().toISOString().slice(0, 10);
                    setPaymentFeedback('', 'error');
                    refreshTotal();
                    paymentModal.classList.add('active');
                });
            });

            document.getElementById('closePaymentModalButton').addEventListener('click', closePaymentModal);

            paymentModal.addEventListener('click', function (event) {
                if (event.target === paymentModal) closePaymentModal();
            });

            paymentForm.addEventListener('submit', function (event) {
                const anyChecked = monthPicker.querySelector('input[type="checkbox"]:checked');
                if (!anyChecked) {
                    event.preventDefault();
                    setPaymentFeedback('Select at least one month to record.', 'error');
                    return;
                }
                submitPaymentButton.disabled = true;
                submitPaymentButton.textContent = 'Saving...';
            });

            function closeSuccessFlashModal() {
                if (successFlashModal) successFlashModal.classList.remove('active');
            }

            if (successFlashCloseButton) {
                successFlashCloseButton.addEventListener('click', closeSuccessFlashModal);
            }
            if (successFlashModal) {
                successFlashModal.addEventListener('click', function (event) {
                    if (event.target === successFlashModal) closeSuccessFlashModal();
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSuccessFlashModal();
                    if (paymentModal.classList.contains('active')) closePaymentModal();
                }
            });
        })();
    </script>
@endsection
