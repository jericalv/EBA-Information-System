@extends('admin.layout')

@section('title', 'Concessionaires')

@section('extra-css')
<style>
    .payments-overview {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .payments-card {
        background: #fff;
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
    .payments-alert-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
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
    .status-badge-overdue { background: #FBEAEA; color: #B3261E; }
    .status-badge-paid { background: #E5F3EA; color: #14532D; }
    .status-badge-due { background: #FDF8EC; color: #92400E; }
    .status-badge-none { background: #F0F2F0; color: var(--muted); }
    .period-cell {
        white-space: nowrap;
    }
    .period-cell .status-badge {
        margin-left: 6px;
        vertical-align: 1px;
    }
    .paid-through-note {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: var(--muted);
    }
    .fee-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .fee-form input {
        width: 120px;
        padding: 8px 10px;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .fee-form input:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
    }
    .fee-form button {
        padding: 8px 12px;
        border: 0;
        border-radius: 6px;
        background: var(--pine);
        color: #fff;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .fee-form button:hover {
        background: var(--pine-strong);
    }
    .concessionaire-table-wrap {
        overflow-x: auto;
    }
    .concessionaire-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }
    .concessionaire-table th,
    .concessionaire-table td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--line);
        text-align: left;
        vertical-align: top;
    }
    .concessionaire-table th {
        background: #FAFCFA;
        color: var(--muted);
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .concessionaire-table tr:last-child td {
        border-bottom: none;
    }
    .payments-table-note {
        color: #64748b;
        font-size: 13px;
        margin-top: 10px;
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
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0 12px;
    }
    tbody tr.hidden {
        display: none;
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
        color: #64748b;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 1px;
        line-height: 1;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }
    .kebab-btn:hover,
    .kebab-btn[aria-expanded="true"] {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #0f172a;
    }
    .kebab-btn:focus-visible {
        outline: 2px solid var(--green);
        outline-offset: 2px;
    }
    .kebab-menu {
        position: fixed;
        z-index: 1200;
        min-width: 200px;
        background: #fff;
        border: 1px solid #e2e8f0;
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
        color: #1e293b;
        text-align: left;
        cursor: pointer;
        transition: background 0.12s ease;
    }
    .kebab-menu button:hover {
        background: #f1f5f9;
    }
    .kebab-menu button svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        color: #64748b;
    }
</style>
@endsection

@section('content')
    <div class="payments-overview">
        <article class="payments-card">
            <span class="payments-card-label">Total collected</span>
            <span class="payments-card-value is-pine">₱{{ number_format((float) $totalCollected, 2) }}</span>
            <span class="payments-card-note">Filtered payment total</span>
        </article>
        <article class="payments-card">
            <span class="payments-card-label">Records found</span>
            <span class="payments-card-value">{{ $payments->total() }}</span>
            <span class="payments-card-note">Across all concessionaires</span>
        </article>
        <article class="payments-card">
            <span class="payments-card-label">Overdue this month</span>
            <span class="payments-card-value {{ $overdueCount > 0 ? 'is-danger' : '' }}">{{ $overdueCount }}</span>
            <span class="payments-card-note">Active concessionaires with a fee and no payment</span>
        </article>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
            <div>
                <strong style="font-size:16px;color:#111827;">Monthly Fee Tracking</strong>
                <div style="font-size:13px;color:#64748b;margin-top:4px;">Set a fee per concessionaire and monitor current-month payment status.</div>
            </div>
            <div style="font-weight:700;color:#0f172a;">{{ $overdueCount }} concessionaire(s) overdue this month</div>
        </div>
        <div class="card-body concessionaire-table-wrap">
            @if ($concessionaires->isEmpty())
                <div class="empty-state" style="margin:18px;">No concessionaires available.</div>
            @else
                <table class="concessionaire-table">
                    <thead>
                        <tr>
                            <th>Concessionaire</th>
                            <th>Monthly Fee</th>
                            <th>Status</th>
                            <th>Paid Through</th>
                            <th>Last Payment</th>
                            <th>Total Paid</th>
                            <th>Set Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($concessionaires as $concessionaire)
                            @php
                                $monthlyFee = (float) ($concessionaire->monthly_fee ?? 0);
                                $paidThisMonth = (int) ($concessionaire->current_month_payment_count ?? 0) > 0;
                                $today = now()->day;

                                $paidThrough = $concessionaire->paid_through_month
                                    ? \Illuminate\Support\Carbon::parse($concessionaire->paid_through_month)->startOfMonth()
                                    : null;
                                $monthsAhead = $paidThrough
                                    ? now()->startOfMonth()->diffInMonths($paidThrough, false)
                                    : 0;

                                if ($monthlyFee <= 0) {
                                    $statusKey = 'no_contract';
                                } elseif ($paidThisMonth) {
                                    $statusKey = 'paid';
                                } elseif ($today >= 25) {
                                    $statusKey = 'due_soon';
                                } else {
                                    $statusKey = 'overdue';
                                }

                                $statusLabel = match ($statusKey) {
                                    'paid' => 'Paid',
                                    'due_soon' => 'Due Soon',
                                    'overdue' => 'Overdue',
                                    default => '—',
                                };
                                $statusClass = match ($statusKey) {
                                    'paid' => 'status-badge-paid',
                                    'due_soon' => 'status-badge-due',
                                    'overdue' => 'status-badge-overdue',
                                    default => 'status-badge-none',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ $concessionaire->initials() }}</div>
                                        <div>
                                            <div class="user-name">{{ $concessionaire->business_name ?: $concessionaire->name }}</div>

                                            <div class="user-email">{{ $concessionaire->name }} · {{ $concessionaire->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $concessionaire->monthly_fee !== null ? '₱' . number_format((float) $concessionaire->monthly_fee, 2) : 'Not set' }}
                                </td>
                                <td>
                                    <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="period-cell">
                                    @if ($paidThrough)
                                        {{ $paidThrough->format('M Y') }}
                                        @if ($monthsAhead > 0)
                                            <span class="status-badge status-badge-paid">+{{ $monthsAhead }} mo advance</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    {{ $concessionaire->last_payment_date ? \Illuminate\Support\Carbon::parse($concessionaire->last_payment_date)->format('M d, Y') : '—' }}
                                </td>
                                <td>
                                    ₱{{ number_format((float) ($concessionaire->total_paid ?? 0), 2) }}
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.monthly-fee', $concessionaire) }}" class="fee-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="monthly_fee" min="0" step="0.01" value="{{ $concessionaire->monthly_fee !== null ? number_format((float) $concessionaire->monthly_fee, 2, '.', '') : '' }}" placeholder="0.00" required>
                                        <button type="submit">Set Fee</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="card payments-table-card" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:flex; justify-content:space-between; align-items:center; margin:10px 24px 8px;">
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <div class="search-box table-search-inline" style="margin:0;">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input id="payments-instant-search" type="text" placeholder="Business name or concessionaire" oninput="filterRows()">
                </div>
                <select id="concessionaire-filter" onchange="filterRows()" style="min-width:220px;padding:10px 12px;border:1px solid #d1d5db;border-radius:10px;background:#fff;color:#0f172a;">
                    <option value="">All Concessionaires</option>
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('admin.payments.history.view') }}" target="_blank" rel="noopener" class="btn btn-outline">View History</a>
                <a href="{{ route('admin.payments.history.pdf') }}" class="btn btn-green">Download History</a>
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
                            <td style="font-weight:700;color:#0f172a;">₱{{ number_format((float) $payment->amount, 2) }}</td>
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
                            <td colspan="9" style="text-align:center;padding:32px;color:#94a3b8;">No payment records found.</td>
                        </tr>
                    @endforelse
                    <tr id="payments-no-results-row" style="display:none;">
                        <td colspan="9" style="text-align:center;padding:32px;color:#94a3b8;">No results found.</td>
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
