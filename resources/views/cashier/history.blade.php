@extends('cashier.layout')

@section('title', 'Payment Logs')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<style>
    /* ---------- Stat cards (same pattern as the dashboard) ---------- */
    .dstat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .dstat-card {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        box-shadow: var(--shadow-card);
        min-width: 0;
    }
    .dstat-label {
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .dstat-value {
        font-family: var(--font-ui);
        font-size: 34px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.03em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .dstat-value.is-pine { color: var(--pine); }
    .dstat-value.is-danger { color: var(--danger); }
    .dstat-foot {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--line);
        font-size: 12px;
        color: var(--muted);
    }
    @media (max-width: 992px) {
        .dstat-grid { grid-template-columns: 1fr; }
    }

    /* ---------- Month filter (same pattern as admin payment logs) ---------- */
    .month-filter-wrap {
        position: relative;
        width: 170px;
        flex-shrink: 0;
    }
    .month-filter-wrap svg {
        position: absolute;
        top: 50%;
        left: 10px;
        width: 16px;
        height: 16px;
        color: var(--muted);
        transform: translateY(-50%);
        pointer-events: none;
    }
    #month-picker {
        width: 100%;
        height: 38px;
        border: 1px solid var(--line-strong);
        border-radius: 8px;
        padding: 0 12px 0 34px;
        font-size: 13.5px;
        color: var(--ink);
        background: var(--card);
        outline: none;
        font-family: inherit;
        cursor: pointer;
    }
    #month-picker:focus {
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.1);
    }

    .period-cell { white-space: nowrap; }
    .period-cell .status-badge {
        margin: 0 0 0 6px;
        vertical-align: 1px;
    }

    /* ---------- Row action three-dot menu (same pattern as Record Payment) ---------- */
    .actions-col { text-align: right; }
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
        width: 188px;
        display: none;
    }
    #rowActionMenu.is-open { display: block; }
</style>
@endsection

@section('content')
    @php
        $monthLabel = $month ? \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('F Y') : null;
        // Exports honor the selected month so the files match the table.
        $exportParams = $month ? ['month' => $month] : [];
    @endphp

    <div class="page-head">
        <div>
            <span class="eyebrow">Collections</span>
            <h1 class="page-title">Payment Logs</h1>
        </div>
        <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
    </div>

    <section class="dstat-grid" aria-label="Payment log statistics">
        <article class="dstat-card">
            <span class="dstat-label">Total collected</span>
            <span class="dstat-value is-pine">&#8369;{{ number_format((float) $totalCollected, 2) }}</span>
            <span class="dstat-foot">{{ $monthLabel ? 'Collected during ' . $monthLabel : 'All recorded payments' }}</span>
        </article>
        <article class="dstat-card">
            <span class="dstat-label">Records found</span>
            <span class="dstat-value">{{ $recentPayments->count() }}</span>
            <span class="dstat-foot">{{ $monthLabel ? 'Payments made in ' . $monthLabel : 'Across all concessionaires' }}</span>
        </article>
        <article class="dstat-card">
            <span class="dstat-label">Overdue</span>
            <span class="dstat-value {{ $overdueCount > 0 ? 'is-danger' : '' }}">{{ $overdueCount }}</span>
            <span class="dstat-foot">Active concessionaires with unpaid past months</span>
        </article>
    </section>

    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <h2 class="panel-title">Payment logs</h2>
                    <p class="panel-sub">Every payment recorded across all concessionaires.</p>
                </div>
                <div style="display:inline-flex;align-items:center;gap:8px;">
                    <a href="{{ route('cashier.payments.history.view', $exportParams) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">View History</a>
                    <a href="{{ route('cashier.payments.history.csv', $exportParams) }}" class="btn btn-secondary btn-sm">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h1M5 3h10l4 4v14a1 1 0 01-1 1H5a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                        </svg>
                        Download CSV
                    </a>
                    <a href="{{ route('cashier.payments.history.pdf', $exportParams) }}" class="btn btn-primary btn-sm">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="history-filter-bar" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <div class="table-search">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" id="search_box" placeholder="Search concessionaire&hellip;" autocomplete="off">
                </div>
                <div class="month-filter-wrap">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <input
                        id="month-picker"
                        type="text"
                        value="{{ $month }}"
                        placeholder="All months"
                        aria-label="Filter payments by month"
                        readonly
                    >
                </div>
                @if ($month)
                    <a href="{{ route('cashier.history') }}" class="btn btn-secondary btn-sm">
                        <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Clear month
                    </a>
                @endif
            </div>

            @if ($recentPayments->isEmpty())
                <div class="empty-state">{{ $monthLabel ? 'No payments recorded in ' . $monthLabel . '.' : 'No payments recorded yet.' }}</div>
            @else
                <table class="payments-table" id="payments_table">
                    <thead>
                        <tr>
                            <th>Concessionaire</th>
                            <th>Amount Paid</th>
                            <th>For Month</th>
                            <th>Payment Date</th>
                            <th>OR Number</th>
                            <th>Recorded At</th>
                            <th class="actions-col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="payments_tbody">
                        @foreach ($recentPayments as $payment)
                            @php
                                $periodMonth = $payment->period_month?->copy()->startOfMonth();
                                $paidMonth = $payment->payment_date?->copy()->startOfMonth();
                                $periodOffset = ($periodMonth && $paidMonth) ? $paidMonth->diffInMonths($periodMonth, false) : 0;
                            @endphp
                            <tr class="payment-row"
                                data-name="{{ strtolower($payment->concessionaire?->business_name ?: $payment->concessionaire?->name) }}">
                                <td><span class="table-strong">{{ $payment->concessionaire?->business_name ?: $payment->concessionaire?->name }}</span></td>
                                <td><span class="table-num is-pine">&#8369;{{ number_format((float) $payment->amount, 2) }}</span></td>
                                <td class="period-cell">
                                    @if ($periodMonth)
                                        <span class="table-num">{{ $periodMonth->format('M Y') }}</span>
                                        @if ($periodOffset > 0)
                                            <span class="status-badge status-badge-paid" style="margin-top:0;">Advance</span>
                                        @elseif ($periodOffset < 0)
                                            <span class="status-badge status-badge-due" style="margin-top:0;">Arrears</span>
                                        @endif
                                    @else
                                        <span class="table-dim">&mdash;</span>
                                    @endif
                                </td>
                                <td><span class="table-num">{{ $payment->payment_date?->format('M d, Y') ?: '—' }}</span></td>
                                <td>
                                    @if ($payment->or_number)
                                        <span class="table-num">{{ $payment->or_number }}</span>
                                    @else
                                        <span class="table-dim">&mdash;</span>
                                    @endif
                                </td>
                                <td><span class="table-num">{{ $payment->created_at?->setTimezone('Asia/Manila')->format('M d, Y h:i A') ?? '—' }}</span></td>
                                <td class="actions-col">
                                    <button
                                        type="button"
                                        class="row-menu-btn"
                                        aria-label="Actions for payment {{ $payment->or_number ?: $payment->id }}"
                                        aria-haspopup="menu"
                                        data-receipt-url="{{ route('cashier.payments.receipt', $payment->id) }}"
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
                <div id="no_results_message" style="display:none;text-align:center;padding:24px;color:var(--muted);font-size:13.5px;">No payments match your search.</div>
            @endif
        </div>
    </div>

    <div id="rowActionMenu" class="pop" role="menu">
        <a id="rowActionReceipt" class="pop-item" role="menuitem" href="#">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/>
            </svg>
            <span>Download receipt</span>
        </a>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Month filter: picking a month reloads the page server-filtered,
    // same pattern as the admin payment logs.
    const monthInput = document.getElementById('month-picker');
    const baseUrl = @json(route('cashier.history'));
    const currentMonth = @json($month);

    if (monthInput && typeof flatpickr !== 'undefined' && typeof monthSelectPlugin !== 'undefined') {
        flatpickr(monthInput, {
            disableMobile: true,
            defaultDate: currentMonth ? currentMonth + '-01' : null,
            plugins: [
                new monthSelectPlugin({
                    shorthand: false,
                    dateFormat: 'Y-m',
                    altFormat: 'F Y',
                }),
            ],
            onChange: function (selectedDates, dateStr) {
                if (dateStr && dateStr !== currentMonth) {
                    window.location.href = baseUrl + '?month=' + encodeURIComponent(dateStr);
                }
            },
        });
    }

    const searchBox = document.getElementById('search_box');
    const rows = document.querySelectorAll('.payment-row');
    const noResultsMsg = document.getElementById('no_results_message');
    const table = document.getElementById('payments_table');

    function filterRows() {
        const query = searchBox.value.toLowerCase();
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';

            if (name.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (table) {
            if (visibleCount === 0 && rows.length > 0) {
                table.style.display = 'none';
                noResultsMsg.style.display = 'block';
            } else {
                table.style.display = '';
                noResultsMsg.style.display = 'none';
            }
        }
    }

    if (searchBox) {
        searchBox.addEventListener('input', filterRows);

        // Prefill from the navbar search palette (?q=…) and filter immediately
        const initialQuery = new URLSearchParams(window.location.search).get('q');
        if (initialQuery) {
            searchBox.value = initialQuery;
            filterRows();
        }
    }
});

// ---- Row action three-dot menu (shared, fixed-position popover) ----
(() => {
    const menu = document.getElementById('rowActionMenu');
    const receiptLink = document.getElementById('rowActionReceipt');
    if (!menu || !receiptLink) return;

    let activeButton = null;

    function closeMenu() {
        menu.classList.remove('is-open');
        if (activeButton) activeButton.classList.remove('is-open');
        activeButton = null;
    }

    function openMenu(button) {
        receiptLink.href = button.dataset.receiptUrl;

        const rect = button.getBoundingClientRect();
        const width = menu.offsetWidth || 188;
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
