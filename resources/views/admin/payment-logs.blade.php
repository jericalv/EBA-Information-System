@extends('admin.layout')

@section('title', 'Payment Logs')

@section('extra-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<style>
    .payments-overview {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .payments-card {
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
    .payments-card-label {
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .payments-card-value {
        font-family: var(--font-ui);
        font-size: 34px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.03em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .payments-card-value.is-pine { color: var(--pine); }
    .payments-card-value.is-danger { color: var(--danger); }
    .payments-card-note {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--line);
        font-size: 12px;
        color: var(--muted);
    }
    @media (max-width: 992px) {
        .payments-overview {
            grid-template-columns: 1fr;
        }
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 5px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-badge::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: currentColor;
    }
    .status-badge-paid { background: #E5F3EA; color: #14532D; }
    .status-badge-due { background: #FDF8EC; color: #92400E; }
    html[data-theme="dark"] .status-badge-paid { background: rgba(30, 149, 96, 0.16); color: #8CD6AF; }
    html[data-theme="dark"] .status-badge-due { background: rgba(227, 164, 72, 0.14); color: #E9C288; }
    .period-cell {
        white-space: nowrap;
    }
    .period-cell .status-badge {
        margin-left: 6px;
        vertical-align: 1px;
    }
    .table-search-inline {
        width: auto;
        max-width: 420px;
        margin: 14px 24px 14px;
    }
    .payments-table-card .card-body > .search-box.table-search-inline {
        width: auto;
        max-width: 420px;
        flex: 0 1 auto;
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 0 12px;
    }
    tbody tr.hidden {
        display: none;
    }
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
        height: 40px;
        border: 1px solid var(--line-strong);
        border-radius: 10px;
        padding: 0 12px 0 34px;
        font-size: 14px;
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
    .month-clear-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 40px;
        padding: 0 12px;
        text-decoration: none;
        border: 1px solid var(--line-strong);
        border-radius: 10px;
        background: var(--card);
        color: var(--muted);
        font: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: color 0.15s ease, border-color 0.15s ease;
    }
    .month-clear-btn:hover {
        color: var(--ink);
        border-color: var(--pine);
    }

    /* Kebab actions menu (same pattern as the Users page) */
    #payments-table th.actions-col,
    #payments-table td.actions-col {
        text-align: center;
        width: 56px;
    }
    .kebab-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: 1px solid transparent;
        border-radius: 6px;
        background: transparent;
        color: var(--muted);
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 1px;
        line-height: 1;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }
    .kebab-btn:hover,
    .kebab-btn[aria-expanded="true"] {
        background: var(--hover-2);
        border-color: var(--line-strong);
        color: var(--ink);
    }
    .kebab-btn:focus-visible {
        outline: 2px solid var(--green);
        outline-offset: 2px;
    }
    .kebab-menu {
        position: fixed;
        z-index: 1200;
        min-width: 200px;
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 10px;
        box-shadow: 0 12px 32px rgba(15,23,42,0.14);
        padding: 6px;
        display: none;
    }
    .kebab-menu.active {
        display: block;
    }
    .kebab-menu button {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 100%;
        padding: 8px 10px;
        border: none;
        border-radius: 6px;
        background: transparent;
        font: inherit;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--ink);
        text-align: left;
        cursor: pointer;
        transition: background 0.12s ease;
    }
    .kebab-menu button:hover {
        background: var(--hover-2);
    }
    .kebab-menu button svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        color: var(--muted);
    }
</style>
@endsection

@section('content')
    <div class="payments-overview">
        <article class="payments-card">
            <span class="payments-card-label">Total collected</span>
            <span class="payments-card-value is-pine">₱{{ number_format((float) $totalCollected, 2) }}</span>
            <span class="payments-card-note">{{ $month ? 'Collected during ' . \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('F Y') : 'All recorded payments' }}</span>
        </article>
        <article class="payments-card">
            <span class="payments-card-label">Records found</span>
            <span class="payments-card-value">{{ $payments->total() }}</span>
            <span class="payments-card-note">{{ $month ? 'Payments made in ' . \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('F Y') : 'Across all concessionaires' }}</span>
        </article>
        <article class="payments-card">
            <span class="payments-card-label">Overdue</span>
            <span class="payments-card-value {{ $overdueCount > 0 ? 'is-danger' : '' }}">{{ $overdueCount }}</span>
            <span class="payments-card-note">Active concessionaires with unpaid past months</span>
        </article>
    </div>

    <div class="card payments-table-card" style="margin-bottom:20px;">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
            <div>
                <strong style="font-size:16px;color:var(--ink);">Payment Logs</strong>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">Every recorded monthly-fee payment — who paid, who recorded it, and which month it covers.</div>
            </div>
        </div>
        <div class="card-body">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin:10px 24px 8px;">
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <div class="search-box table-search-inline" style="margin:0;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input id="payments-instant-search" type="text" placeholder="Business name or concessionaire" oninput="filterRows()">
                    </div>
                    <select id="concessionaire-filter" onchange="filterRows()" style="min-width:220px;padding:10px 12px;border:1px solid var(--line-strong);border-radius:10px;background: var(--card);color:var(--ink);">
                        <option value="">All Concessionaires</option>
                    </select>
                    <div class="month-filter-wrap">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                        <a href="{{ route('admin.payment-logs') }}" class="month-clear-btn">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            Clear month
                        </a>
                    @endif
                </div>
                @php
                    // Exports honor the selected month so the files match the table.
                    $exportParams = $month ? ['month' => $month] : [];
                @endphp
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('admin.payments.history.view', $exportParams) }}" target="_blank" rel="noopener" class="btn btn-outline">View History</a>
                    <a href="{{ route('admin.payments.history.csv', $exportParams) }}" class="btn btn-outline">Download CSV</a>
                    <a href="{{ route('admin.payments.history.pdf', $exportParams) }}" class="btn btn-green">Download PDF</a>
                </div>
            </div>
            <table id="payments-table">
                <thead>
                    <tr>
                        <th>Concessionaire</th>
                        <th>Amount</th>
                        <th>Payment Type</th>
                        <th>OR Number</th>
                        <th>For Month</th>
                        <th>Payment Date</th>
                        <th>Recorded By</th>
                        <th>Notes</th>
                        <th class="actions-col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr
                            data-row="payment"
                            data-name="{{ strtolower(trim(($payment->concessionaire?->business_name ?? '') . ' ' . ($payment->concessionaire?->name ?? ''))) }}"
                            data-email="{{ strtolower($payment->concessionaire?->email ?: '') }}"
                            data-business="{{ strtolower($payment->concessionaire?->business_name ?? '') }}"
                        >
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">{{ $payment->concessionaire?->initials() ?? 'P' }}</div>
                                    <div>
                                        <div class="user-name concessionaire-name">{{ $payment->concessionaire?->business_name ?: $payment->concessionaire?->name }}</div>
                                        <div class="user-email">{{ $payment->concessionaire?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight:700;color:var(--ink);">₱{{ number_format((float) $payment->amount, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                            <td>{{ $payment->or_number ?: '—' }}</td>
                            @php
                                $periodMonth = $payment->period_month?->copy()->startOfMonth();
                                $paidMonth = $payment->payment_date?->copy()->startOfMonth();
                                $periodOffset = ($periodMonth && $paidMonth) ? $paidMonth->diffInMonths($periodMonth, false) : 0;
                            @endphp
                            <td class="period-cell">
                                @if ($periodMonth)
                                    {{ $periodMonth->format('M Y') }}
                                    @if ($periodOffset > 0)
                                        <span class="status-badge status-badge-paid">Advance</span>
                                    @elseif ($periodOffset < 0)
                                        <span class="status-badge status-badge-due">Arrears</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                            <td>{{ $payment->recordedBy?->name ?: '—' }}</td>
                            <td style="max-width:260px;">{{ $payment->notes ?: '—' }}</td>
                            <td class="actions-col">
                                <button
                                    type="button"
                                    class="kebab-btn"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    aria-label="Actions for payment {{ $payment->or_number ?: $payment->id }}"
                                    data-invoice-url="{{ route('admin.payments.receipt', $payment->id) }}"
                                >⋯</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:32px;color:var(--faint);">
                                {{ $month ? 'No payments recorded in ' . \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('F Y') . '.' : 'No payment records found.' }}
                            </td>
                        </tr>
                    @endforelse
                    <tr id="payments-no-results-row" style="display:none;">
                        <td colspan="9" style="text-align:center;padding:32px;color:var(--faint);">No results found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="pagination-wrap">
                {{ $payments->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- Shared row-actions menu (positioned next to the clicked ⋯ button) --}}
    <div class="kebab-menu" id="paymentActionsMenu" role="menu">
        <button type="button" id="menuDownloadInvoice" role="menuitem">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download Invoice
        </button>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
    // Instant search filter
    function filterRows() {
        const input = document.getElementById('payments-instant-search');
        const concessionaireFilter = document.getElementById('concessionaire-filter');
        const rows = document.querySelectorAll('tr[data-row="payment"]');
        const noResultsRow = document.getElementById('payments-no-results-row');

        if (!input || rows.length === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
            return;
        }

        const query = input.value.trim().toLowerCase();
        const selectedConcessionaire = (concessionaireFilter?.value || '').trim().toLowerCase();
        let visibleRows = 0;

        rows.forEach((row) => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const businessName = row.querySelector('.concessionaire-name')?.textContent.trim().toLowerCase() || '';
            const matchesQuery = !query || name.includes(query) || email.includes(query);
            const matchesConcessionaire = !selectedConcessionaire || businessName === selectedConcessionaire;
            const matches = matchesQuery && matchesConcessionaire;

            row.classList.toggle('hidden', !matches);
            if (matches) {
                visibleRows += 1;
            }
        });

        if (noResultsRow) {
            noResultsRow.style.display = visibleRows === 0 ? '' : 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.getElementById('concessionaire-filter');
        const seen = new Set();

        if (dropdown) {
            document.querySelectorAll('#payments-table tbody tr[data-row="payment"]').forEach((row) => {
                const name = row.querySelector('.concessionaire-name')?.textContent.trim();
                if (name && !seen.has(name)) {
                    seen.add(name);
                }
            });

            Array.from(seen)
                .sort((a, b) => a.localeCompare(b))
                .forEach((name) => {
                    const opt = document.createElement('option');
                    opt.value = name;
                    opt.textContent = name;
                    dropdown.appendChild(opt);
                });
        }

        filterRows();

        // Month filter: picking a month reloads the page server-filtered,
        // same pattern as the POS transaction logs date range.
        const monthInput = document.getElementById('month-picker');
        const baseUrl = @json(route('admin.payment-logs'));
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
    });

    // === Row actions (kebab) menu ===
    const paymentActionsMenu = document.getElementById('paymentActionsMenu');
    const menuDownloadInvoice = document.getElementById('menuDownloadInvoice');

    let activeKebab = null;
    let menuInvoiceUrl = null;

    function closeActionsMenu() {
        paymentActionsMenu.classList.remove('active');
        if (activeKebab) {
            activeKebab.setAttribute('aria-expanded', 'false');
            activeKebab = null;
        }
    }

    function openActionsMenu(button) {
        menuInvoiceUrl = button.dataset.invoiceUrl;

        paymentActionsMenu.classList.add('active');
        activeKebab = button;
        button.setAttribute('aria-expanded', 'true');

        const rect = button.getBoundingClientRect();
        const menuRect = paymentActionsMenu.getBoundingClientRect();
        let left = rect.right - menuRect.width;
        let top = rect.bottom + 6;
        if (left < 8) left = 8;
        if (top + menuRect.height > window.innerHeight - 8) {
            top = rect.top - menuRect.height - 6;
        }
        paymentActionsMenu.style.left = `${left}px`;
        paymentActionsMenu.style.top = `${top}px`;
    }

    document.querySelectorAll('.kebab-btn').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            if (activeKebab === button) {
                closeActionsMenu();
            } else {
                closeActionsMenu();
                openActionsMenu(button);
            }
        });
    });

    menuDownloadInvoice.addEventListener('click', () => {
        if (menuInvoiceUrl) {
            window.location.href = menuInvoiceUrl;
        }
        closeActionsMenu();
    });

    document.addEventListener('click', (event) => {
        if (paymentActionsMenu.classList.contains('active') && !paymentActionsMenu.contains(event.target)) {
            closeActionsMenu();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeActionsMenu();
        }
    });
    window.addEventListener('scroll', closeActionsMenu, true);
    window.addEventListener('resize', closeActionsMenu);
</script>
@endsection
