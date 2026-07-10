@extends('cashier.layout')

@section('title', 'Record Payment')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* ---------- Pre-save confirmation modal (stacked above payment modal) ---------- */
    #paymentConfirmModal { z-index: 2300; }
    #paymentConfirmModal .modal { width: min(460px, 100%); }
    .confirm-lead {
        font-size: 13.5px;
        color: var(--ink);
        line-height: 1.55;
        margin-bottom: 14px;
    }
    .confirm-months {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }
    .confirm-months .cm-chip {
        font-family: var(--font-mono);
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 9px;
        border-radius: 999px;
        background: var(--pine-soft, #EAF3ED);
        color: #14532D;
        border: 1px solid #CDE3D4;
    }
    .confirm-total {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid var(--line-strong);
        border-radius: 8px;
        background: #FAFCFA;
        margin-bottom: 8px;
    }
    .confirm-total .ct-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--muted);
    }
    .confirm-total .ct-value {
        font-family: var(--font-mono);
        font-size: 20px;
        font-weight: 800;
        color: var(--pine);
    }
    .confirm-hint {
        font-size: 12.5px;
        color: #92400E;
        background: #FDF8EC;
        border: 1px solid #F0DCB0;
        border-radius: 6px;
        padding: 9px 12px;
        line-height: 1.5;
    }

    /* ---------- Contract period cell + edit button ---------- */
    .contract-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .contract-edit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        flex: 0 0 auto;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        background: #fff;
        color: var(--muted);
        cursor: pointer;
        transition: color 0.12s ease, border-color 0.12s ease;
    }
    .contract-edit-btn:hover {
        color: var(--pine);
        border-color: var(--pine);
    }
    .contract-edit-btn svg {
        width: 13px;
        height: 13px;
    }

    /* ---------- Month calendar (modal) ---------- */
    .mcal {
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #FAFCFA;
        padding: 12px;
    }
    .mcal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .mcal-year {
        font-family: var(--font-mono);
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.04em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .mcal-nav {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        background: #fff;
        color: var(--ink);
        cursor: pointer;
        transition: background-color 0.12s ease, border-color 0.12s ease, opacity 0.12s ease;
    }
    .mcal-nav svg { width: 15px; height: 15px; }
    .mcal-nav:hover:not(:disabled) { background: #F0F4F1; border-color: #AEC1B4; }
    .mcal-nav:disabled { opacity: 0.4; cursor: not-allowed; }

    .mcal-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 6px;
    }
    .mcal-cell {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
        padding: 9px 10px;
        border: 1px solid var(--line-strong);
        border-radius: 7px;
        background: #fff;
        color: var(--ink);
        cursor: pointer;
        text-align: left;
        transition: border-color 0.12s ease, background-color 0.12s ease, box-shadow 0.12s ease;
    }
    .mcal-cell:hover:not(.is-locked):not(.is-selected) {
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.08);
    }
    .mcal-m {
        font-size: 14px;
        font-weight: 700;
        line-height: 1;
    }
    .mcal-sub {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-family: var(--font-mono);
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--faint);
        line-height: 1;
    }
    .mcal-sub svg { width: 10px; height: 10px; }

    /* Unpaid past months (red) — draw attention. */
    .mcal-cell.st-arrears { border-color: #E39B9B; background: #FBE7E7; color: #8A1D18; }
    .mcal-cell.st-arrears .mcal-sub { color: #B3261E; }
    /* Current month (amber). */
    .mcal-cell.st-current { border-color: #E4B759; background: #FBEFCF; color: #7A3D0B; }
    .mcal-cell.st-current .mcal-sub { color: #92400E; }
    /* Future months available to prepay (blue). */
    .mcal-cell.st-advance { border-color: #9DC2E6; background: #E9F2FB; color: #1B4F86; }
    .mcal-cell.st-advance .mcal-sub { color: #1D5C9E; }

    /* Already recorded — locked (grey-green, muted). */
    .mcal-cell.st-paid,
    .mcal-cell.is-locked {
        cursor: default;
    }
    .mcal-cell.st-paid {
        border-color: #BAC9BE;
        background: #EAEFEB;
        color: #4A5B4F;
    }
    .mcal-cell.st-paid .mcal-sub { color: #5C7061; }
    /* Outside the contract window. */
    .mcal-cell.st-before,
    .mcal-cell.st-after {
        background: #F6F7F6;
        border-style: dashed;
        border-color: var(--line);
        color: var(--faint);
    }
    .mcal-cell.st-before .mcal-sub,
    .mcal-cell.st-after .mcal-sub { color: var(--faint); }

    /* Chosen to be recorded — overrides the state tint. */
    .mcal-cell.is-selected {
        border-color: var(--pine);
        background: var(--pine);
        color: #fff;
        box-shadow: 0 1px 2px rgba(10, 92, 47, 0.25);
    }
    .mcal-cell.is-selected .mcal-sub { color: rgba(255, 255, 255, 0.85); }

    .mcal-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 16px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--line);
    }
    .mcal-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--muted);
    }
    .mcal-legend .sw {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        border: 1px solid;
        flex-shrink: 0;
    }
    .mcal-legend .sw-selected { background: var(--pine); border-color: var(--pine); }
    .mcal-legend .sw-arrears { background: #FBE7E7; border-color: #E39B9B; }
    .mcal-legend .sw-current { background: #FBEFCF; border-color: #E4B759; }
    .mcal-legend .sw-advance { background: #E9F2FB; border-color: #9DC2E6; }
    .mcal-legend .sw-paid { background: #EAEFEB; border-color: #BAC9BE; }

    .mcal-summary {
        margin-top: 10px;
        font-size: 12.5px;
        color: var(--muted);
        line-height: 1.5;
    }
    .mcal-summary strong { color: var(--ink); font-weight: 700; }
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

    /* ---------- Row action three-dot menu ---------- */
    .row-menu-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--muted);
        cursor: pointer;
        transition: background-color 0.12s ease, color 0.12s ease, border-color 0.12s ease;
    }
    .row-menu-btn svg { width: 17px; height: 17px; }
    .row-menu-btn:hover,
    .row-menu-btn.is-open {
        background: var(--pine-soft);
        color: var(--pine);
        border-color: var(--line-strong);
    }
    .row-menu-btn:focus-visible { outline: 2px solid rgba(10, 92, 47, 0.45); outline-offset: 2px; }

    #rowActionMenu {
        position: fixed;
        z-index: 2400;
        width: 172px;
        display: none;
    }
    #rowActionMenu.is-open { display: block; }
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
                            <th>Record</th>
                            <th style="text-align:right;">Action</th>
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
                                $plan = $paymentPlans[$concessionaire->id] ?? null;
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
                                    <div class="contract-cell">
                                        @if ($contractStart && $contractEnd)
                                            <span class="table-num">{{ $contractStart->format('M d, Y') }} &rarr; {{ $contractEnd->format('M d, Y') }}</span>
                                        @else
                                            <span class="table-dim">&mdash;</span>
                                        @endif
                                        @if ($latestApplication && in_array($latestApplication->status, ['pending', 'under_review', 'approved', 'registered'], true))
                                            <button
                                                type="button"
                                                class="contract-edit-btn"
                                                aria-label="Set contract period"
                                                title="{{ $contractStart && $contractEnd ? 'Edit contract period' : 'Set contract period' }}"
                                                data-application-id="{{ $latestApplication->id }}"
                                                data-business="{{ $concessionaire->business_name ?: $concessionaire->name }}"
                                                data-start="{{ $contractStart?->format('Y-m-d') }}"
                                                data-end="{{ $contractEnd?->format('Y-m-d') }}"
                                            >
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
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
                                    @if ($monthlyFee > 0 && ! empty($plan['has_selectable']))
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm record-payment-btn"
                                            data-user-id="{{ $concessionaire->id }}"
                                        >
                                            Payment
                                        </button>
                                    @else
                                        <span class="table-dim">&mdash;</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <button
                                        type="button"
                                        class="row-menu-btn"
                                        aria-label="More actions"
                                        aria-haspopup="menu"
                                        data-view-url="{{ route('cashier.payments.concessionaire.history.view', $concessionaire->id) }}"
                                        data-download-url="{{ route('cashier.payments.concessionaire.history.pdf', $concessionaire->id) }}"
                                    >
                                        <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div id="rowActionMenu" class="pop" role="menu">
        <a id="rowActionView" class="pop-item" role="menuitem" target="_blank" rel="noopener" href="#">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span>View history</span>
        </a>
        <a id="rowActionDownload" class="pop-item" role="menuitem" href="#">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/>
            </svg>
            <span>Download PDF</span>
        </a>
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
                    <div class="mcal">
                        <div class="mcal-head">
                            <button type="button" class="mcal-nav" id="mcalPrev" aria-label="Previous year">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="mcal-year" id="mcalYear">—</span>
                            <button type="button" class="mcal-nav" id="mcalNext" aria-label="Next year">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <div class="mcal-grid" id="mcalGrid"></div>
                        <div class="mcal-legend">
                            <span><i class="sw sw-selected"></i>Selected</span>
                            <span><i class="sw sw-arrears"></i>Unpaid / arrears</span>
                            <span><i class="sw sw-current"></i>This month</span>
                            <span><i class="sw sw-advance"></i>Advance</span>
                            <span><i class="sw sw-paid"></i>Paid</span>
                        </div>
                    </div>
                    <div class="mcal-summary" id="mcalSummary"></div>
                    <small class="field-help">Click any month to add or remove it. Overdue and the current month start selected; pick future months to pay in advance. Paid months are locked.</small>
                    <div id="selectedInputs"></div>
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
                    <button type="submit" class="btn btn-primary" id="submitPaymentButton">Review Payment</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-backdrop" id="paymentConfirmModal">
        <div class="modal">
            <h3>Confirm Payment</h3>
            <div class="notice" id="confirmConcessionaireLine"></div>
            <p class="confirm-lead">Record the following <strong id="confirmCountText">0 months</strong>. Please double-check — only the months listed here will be billed.</p>
            <div class="confirm-months" id="confirmMonths"></div>
            <div class="confirm-total">
                <span class="ct-label">Total to record</span>
                <span class="ct-value" id="confirmTotal">&#8369;0.00</span>
            </div>
            <div class="confirm-hint">If the payer is only settling specific months, go back and deselect the ones they are not paying for today.</div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="confirmGoBackButton">Go back</button>
                <button type="button" class="btn btn-primary" id="confirmSaveButton">Confirm &amp; Save</button>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="contractModal">
        <div class="modal">
            <h3>Set Contract Period</h3>
            <div class="notice" id="contractModalSubtitle"></div>
            <form method="POST" action="#" id="contractForm">
                @csrf
                @method('PATCH')

                <div class="field">
                    <label>Concessionaire</label>
                    <input type="text" id="contractBusinessName" readonly>
                </div>

                <div class="field">
                    <label for="contract_range">Contract period <span class="required">*</span></label>
                    <input id="contract_range" type="text" placeholder="Select start and end dates" autocomplete="off">
                    <small class="field-help">Pick the contract start date, then the end date. The months available in Record Payment follow this window.</small>
                    <input type="hidden" name="contract_period_start" id="contractStartInput">
                    <input type="hidden" name="contract_period_end" id="contractEndInput">
                </div>

                <div id="contractFeedback" class="modal-feedback"></div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" id="closeContractModalButton">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitContractButton">Save Period</button>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // ---- Contract period modal (flatpickr range) ----
        (() => {
            const contractModal = document.getElementById('contractModal');
            if (!contractModal) return;

            const contractForm = document.getElementById('contractForm');
            const rangeInput = document.getElementById('contract_range');
            const startInput = document.getElementById('contractStartInput');
            const endInput = document.getElementById('contractEndInput');
            const businessInput = document.getElementById('contractBusinessName');
            const subtitle = document.getElementById('contractModalSubtitle');
            const feedback = document.getElementById('contractFeedback');
            const submitButton = document.getElementById('submitContractButton');
            const actionTemplate = "{{ route('cashier.partnerships.contract-period', ['application' => '__ID__']) }}";

            let picker = null;
            if (typeof flatpickr !== 'undefined') {
                picker = flatpickr(rangeInput, {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    onChange(selectedDates) {
                        if (selectedDates.length === 2) {
                            startInput.value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                            endInput.value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
                        } else {
                            startInput.value = '';
                            endInput.value = '';
                        }
                    },
                });
            }

            function setFeedback(message) {
                feedback.classList.remove('error', 'success');
                if (!message) {
                    feedback.textContent = '';
                    return;
                }
                feedback.textContent = message;
                feedback.classList.add('error');
            }

            function closeContractModal() {
                contractModal.classList.remove('active');
                if (picker) picker.clear();
                startInput.value = '';
                endInput.value = '';
                setFeedback('');
                submitButton.disabled = false;
                submitButton.textContent = 'Save Period';
            }

            document.querySelectorAll('.contract-edit-btn').forEach((button) => {
                button.addEventListener('click', function () {
                    contractForm.action = actionTemplate.replace('__ID__', this.dataset.applicationId);
                    businessInput.value = this.dataset.business || '';
                    subtitle.textContent = (this.dataset.start && this.dataset.end)
                        ? 'Update the contract window for ' + (this.dataset.business || 'this concessionaire') + '.'
                        : 'Set the contract window for ' + (this.dataset.business || 'this concessionaire') + '.';

                    setFeedback('');
                    startInput.value = this.dataset.start || '';
                    endInput.value = this.dataset.end || '';
                    if (picker) {
                        if (this.dataset.start && this.dataset.end) {
                            picker.setDate([this.dataset.start, this.dataset.end], false);
                        } else {
                            picker.clear();
                        }
                    }

                    contractModal.classList.add('active');
                });
            });

            document.getElementById('closeContractModalButton').addEventListener('click', closeContractModal);

            contractModal.addEventListener('click', function (event) {
                if (event.target === contractModal) closeContractModal();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && contractModal.classList.contains('active')) {
                    closeContractModal();
                }
            });

            contractForm.addEventListener('submit', function (event) {
                if (!startInput.value || !endInput.value) {
                    event.preventDefault();
                    setFeedback('Select both a start and an end date for the contract period.');
                    return;
                }
                submitButton.disabled = true;
                submitButton.textContent = 'Saving...';
            });
        })();

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
            const paymentTotal = document.getElementById('paymentTotal');
            const paymentCountLabel = document.getElementById('paymentCountLabel');
            const successFlashModal = document.getElementById('successFlashModal');
            const successFlashCloseButton = document.getElementById('successFlashCloseButton');

            const paymentConfirmModal = document.getElementById('paymentConfirmModal');
            const confirmConcessionaireLine = document.getElementById('confirmConcessionaireLine');
            const confirmCountText = document.getElementById('confirmCountText');
            const confirmMonths = document.getElementById('confirmMonths');
            const confirmTotal = document.getElementById('confirmTotal');
            const confirmGoBackButton = document.getElementById('confirmGoBackButton');
            const confirmSaveButton = document.getElementById('confirmSaveButton');

            const mcalGrid = document.getElementById('mcalGrid');
            const mcalYear = document.getElementById('mcalYear');
            const mcalPrev = document.getElementById('mcalPrev');
            const mcalNext = document.getElementById('mcalNext');
            const mcalSummary = document.getElementById('mcalSummary');
            const selectedInputs = document.getElementById('selectedInputs');

            const MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const CHECK_SVG = '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
            const SUB_LABEL = { paid: 'Paid', arrears: 'Arrears', current: 'This month', advance: 'Advance', before: '—', after: '—' };

            const peso = (value) => '₱' + Number(value || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });

            const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (ch) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[ch]));

            const monthKey = (year, monthIndex) => year + '-' + String(monthIndex + 1).padStart(2, '0');
            const keyLabel = (key) => MONTHS_SHORT[Number(key.slice(5, 7)) - 1] + ' ' + key.slice(0, 4);

            // Holds the modal's working state while it is open.
            let cal = null;

            function classify(plan, key) {
                if (plan.contract_start && key < plan.contract_start) return 'before';
                if (plan.contract_end && key > plan.contract_end) return 'after';
                if (plan.paidSet.has(key)) return 'paid';
                if (key < plan.current_month) return 'arrears';
                if (key === plan.current_month) return 'current';
                return 'advance';
            }

            const isSelectable = (state) => state === 'arrears' || state === 'current' || state === 'advance';

            function renderCalendar() {
                if (!cal) return;
                mcalYear.textContent = cal.year;
                mcalPrev.disabled = cal.year <= cal.minYear;
                mcalNext.disabled = cal.year >= cal.maxYear;

                let html = '';
                for (let mi = 0; mi < 12; mi++) {
                    const key = monthKey(cal.year, mi);
                    const state = classify(cal.plan, key);
                    const selectable = isSelectable(state);
                    const selected = cal.selected.has(key);
                    const classes = ['mcal-cell', 'st-' + state];
                    if (selected) classes.push('is-selected');
                    if (!selectable) classes.push('is-locked');
                    const sub = selected
                        ? CHECK_SVG + 'Selected'
                        : (state === 'paid' ? CHECK_SVG + 'Paid' : SUB_LABEL[state]);
                    html += '<button type="button" class="' + classes.join(' ') + '" data-key="' + key + '"'
                        + (selectable ? '' : ' disabled') + '>'
                        + '<span class="mcal-m">' + MONTHS_SHORT[mi] + '</span>'
                        + '<span class="mcal-sub">' + sub + '</span>'
                        + '</button>';
                }
                mcalGrid.innerHTML = html;
                refreshTotal();
            }

            function refreshTotal() {
                const count = cal ? cal.selected.size : 0;
                const total = cal ? count * cal.fee : 0;
                paymentTotal.textContent = peso(total);
                paymentCountLabel.textContent = count + ' ' + (count === 1 ? 'month' : 'months') + ' selected';
                submitPaymentButton.disabled = count === 0;
                renderSummary();
                syncHiddenInputs();
            }

            function renderSummary() {
                if (!cal || cal.selected.size === 0) {
                    mcalSummary.innerHTML = 'No months selected yet.';
                    return;
                }
                const labels = [...cal.selected].sort().map(keyLabel);
                mcalSummary.innerHTML = '<strong>Recording:</strong> ' + escapeHtml(labels.join(', '));
            }

            function syncHiddenInputs() {
                if (!cal) {
                    selectedInputs.innerHTML = '';
                    return;
                }
                const fee = cal.fee.toFixed(2);
                selectedInputs.innerHTML = [...cal.selected].sort().map((key) =>
                    '<input type="hidden" name="months[]" value="' + escapeHtml(key) + '">'
                    + '<input type="hidden" name="amounts[' + escapeHtml(key) + ']" value="' + fee + '">'
                ).join('');
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
                cal = null;
                mcalGrid.innerHTML = '';
                selectedInputs.innerHTML = '';
                setPaymentFeedback('', 'error');
                closeConfirmModal();
            }

            function closeConfirmModal() {
                paymentConfirmModal.classList.remove('active');
                confirmMonths.innerHTML = '';
                confirmSaveButton.disabled = false;
                confirmSaveButton.textContent = 'Confirm & Save';
            }

            mcalGrid.addEventListener('click', (event) => {
                const cell = event.target.closest('.mcal-cell');
                if (!cell || cell.disabled || !cal) return;
                const key = cell.dataset.key;
                if (cal.selected.has(key)) {
                    cal.selected.delete(key);
                } else {
                    cal.selected.add(key);
                }
                renderCalendar();
            });

            mcalPrev.addEventListener('click', () => {
                if (!cal || cal.year <= cal.minYear) return;
                cal.year -= 1;
                renderCalendar();
            });
            mcalNext.addEventListener('click', () => {
                if (!cal || cal.year >= cal.maxYear) return;
                cal.year += 1;
                renderCalendar();
            });

            document.querySelectorAll('.record-payment-btn').forEach((button) => {
                button.addEventListener('click', function () {
                    const plan = paymentPlans[this.dataset.userId];
                    if (!plan) return;

                    paymentForm.reset();
                    paymentConcessionaireId.value = this.dataset.userId;
                    paymentConcessionaireName.value = `${plan.business} (${plan.name})`;

                    const owed = Number(plan.owed_count || 0);
                    let subtitle = `Recording payment for ${plan.business}. Select the month(s) being paid.`;
                    if (owed > 0) {
                        subtitle += ` ${owed} ${owed === 1 ? 'month is' : 'months are'} overdue.`;
                    }
                    paymentSubtitle.textContent = subtitle;

                    const currentYear = Number(plan.current_month.slice(0, 4));
                    const minYear = plan.contract_start ? Number(plan.contract_start.slice(0, 4)) : currentYear;
                    const maxYear = plan.contract_end ? Number(plan.contract_end.slice(0, 4)) : currentYear + 1;
                    const initYear = Math.min(Math.max(currentYear, minYear), maxYear);

                    cal = {
                        plan: Object.assign({}, plan, { paidSet: new Set(plan.paid_months || []) }),
                        fee: Number(plan.monthly_fee || 0),
                        year: initYear,
                        minYear,
                        maxYear,
                        selected: new Set(),
                    };

                    paymentDateInput.value = new Date().toISOString().slice(0, 10);
                    setPaymentFeedback('', 'error');
                    renderCalendar();
                    paymentModal.classList.add('active');
                });
            });

            document.getElementById('closePaymentModalButton').addEventListener('click', closePaymentModal);

            paymentModal.addEventListener('click', function (event) {
                if (event.target === paymentModal) closePaymentModal();
            });

            // "Review Payment" opens a separate confirmation modal instead of saving directly.
            paymentForm.addEventListener('submit', function (event) {
                event.preventDefault();
                if (!cal || cal.selected.size === 0) {
                    setPaymentFeedback('Select at least one month to record.', 'error');
                    return;
                }
                const labels = [...cal.selected].sort();
                confirmConcessionaireLine.textContent = paymentConcessionaireName.value;
                confirmCountText.textContent = labels.length + ' ' + (labels.length === 1 ? 'month' : 'months');
                confirmMonths.innerHTML = labels
                    .map((key) => '<span class="cm-chip">' + escapeHtml(keyLabel(key)) + '</span>')
                    .join('');
                confirmTotal.textContent = peso(labels.length * cal.fee);
                confirmSaveButton.disabled = false;
                confirmSaveButton.textContent = 'Confirm & Save';
                paymentConfirmModal.classList.add('active');
            });

            confirmGoBackButton.addEventListener('click', closeConfirmModal);

            paymentConfirmModal.addEventListener('click', function (event) {
                if (event.target === paymentConfirmModal) closeConfirmModal();
            });

            confirmSaveButton.addEventListener('click', function () {
                confirmSaveButton.disabled = true;
                confirmSaveButton.textContent = 'Saving...';
                submitPaymentButton.disabled = true;
                // Native submit bypasses the submit handler above and posts the form.
                paymentForm.submit();
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
                if (event.key !== 'Escape') return;
                closeSuccessFlashModal();
                if (paymentConfirmModal.classList.contains('active')) {
                    closeConfirmModal();
                } else if (paymentModal.classList.contains('active')) {
                    closePaymentModal();
                }
            });
        })();

        // ---- Row action three-dot menu (shared, fixed-position popover) ----
        (() => {
            const menu = document.getElementById('rowActionMenu');
            const viewLink = document.getElementById('rowActionView');
            const downloadLink = document.getElementById('rowActionDownload');
            if (!menu) return;

            let activeButton = null;

            function closeMenu() {
                menu.classList.remove('is-open');
                if (activeButton) activeButton.classList.remove('is-open');
                activeButton = null;
            }

            function openMenu(button) {
                viewLink.href = button.dataset.viewUrl;
                downloadLink.href = button.dataset.downloadUrl;

                const rect = button.getBoundingClientRect();
                const width = menu.offsetWidth || 172;
                // Right-align the menu to the button, keeping it on-screen.
                let left = rect.right - width;
                if (left < 8) left = 8;
                let top = rect.bottom + 6;

                menu.style.left = left + 'px';
                menu.style.top = top + 'px';
                menu.classList.add('is-open');

                // Flip above if it would overflow the viewport bottom.
                const menuRect = menu.getBoundingClientRect();
                if (menuRect.bottom > window.innerHeight - 8) {
                    menu.style.top = (rect.top - menuRect.height - 6) + 'px';
                }

                button.classList.add('is-open');
                activeButton = button;
            }

            document.querySelectorAll('.row-menu-btn').forEach((button) => {
                button.addEventListener('click', function (event) {
                    event.stopPropagation();
                    if (activeButton === this) {
                        closeMenu();
                    } else {
                        closeMenu();
                        openMenu(this);
                    }
                });
            });

            menu.addEventListener('click', (event) => event.stopPropagation());
            document.addEventListener('click', closeMenu);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeMenu();
            });
            window.addEventListener('resize', closeMenu);
            window.addEventListener('scroll', closeMenu, true);
        })();
    </script>
@endsection
