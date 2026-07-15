@extends('cashier.layout')

@section('title', 'Payment History')

@section('extra-css')
<style>
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
    <div class="page-head">
        <div>
            <span class="eyebrow">Collections</span>
            <h1 class="page-title">Payment History</h1>
        </div>
        <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
    </div>

    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <h2 class="panel-title">Recent payment history</h2>
                    <p class="panel-sub">Every payment recorded across all concessionaires.</p>
                </div>
                <div style="display:inline-flex;align-items:center;gap:8px;">
                    <a href="{{ route('cashier.payments.history.view') }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">View History</a>
                    <a href="{{ route('cashier.payments.history.csv') }}" class="btn btn-secondary btn-sm">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h1M5 3h10l4 4v14a1 1 0 01-1 1H5a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                        </svg>
                        Download CSV
                    </a>
                    <a href="{{ route('cashier.payments.history.pdf') }}" class="btn btn-primary btn-sm">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="history-filter-bar">
                <div class="table-search">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" id="search_box" placeholder="Search concessionaire&hellip;" autocomplete="off">
                </div>
            </div>

            @if ($recentPayments->isEmpty())
                <div class="empty-state">No payments recorded yet.</div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
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
