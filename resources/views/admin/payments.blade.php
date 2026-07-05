@extends('admin.layout')

@section('title', 'Concessionaires')

@section('extra-css')
<style>
    .payments-overview {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    .payments-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border-top-width: 3px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        cursor: pointer;
    }
    .payments-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 80px;
        opacity: 0.03;
        pointer-events: none;
        background: linear-gradient(180deg, transparent 0%, currentColor 100%);
        transition: opacity 0.3s ease;
    }
    .payments-card:nth-child(1) { border-top-color: #10b981; color: #10b981; }
    .payments-card:nth-child(2) { border-top-color: #3b82f6; color: #3b82f6; }
    .payments-card:nth-child(3) { border-top-color: #f59e0b; color: #f59e0b; }
    .payments-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12), 0 0 40px rgba(0,0,0,0.04);
        border-color: currentColor;
    }
    .payments-card:hover::after {
        opacity: 0.05;
    }
    .payments-card:active {
        transform: translateY(-2px) scale(1.01);
        transition: all 0.1s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .payments-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 8px;
    }
    .payments-card-info {
        flex: 1;
        min-width: 0;
    }
    .payments-card-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 6px;
    }
    .payments-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .payments-card:hover .payments-card-icon {
        transform: scale(1.1) rotate(5deg);
    }
    .payments-card:active .payments-card-icon {
        transform: scale(1.05) rotate(2deg);
    }
    .payments-card:nth-child(1) .payments-card-icon { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
        color: #fff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    }
    .payments-card:nth-child(2) .payments-card-icon { 
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); 
        color: #fff;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
    }
    .payments-card:nth-child(3) .payments-card-icon { 
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); 
        color: #fff;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
    }
    .payments-card-value {
        font-size: 36px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .payments-card:hover .payments-card-value {
        transform: translateX(2px);
        color: #1e293b;
    }
    .payments-card-note {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
        margin-bottom: 8px;
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
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-badge-overdue { background: #fee2e2; color: #991b1b; }
    .status-badge-paid { background: #dcfce7; color: #166534; }
    .status-badge-due { background: #fef3c7; color: #92400e; }
    .status-badge-none { background: #e2e8f0; color: #475569; }
    .fee-form {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .fee-form input {
        width: 120px;
        padding: 8px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font: inherit;
    }
    .fee-form button {
        padding: 8px 12px;
        border: 0;
        border-radius: 8px;
        background: #0a5c2f;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
    }
    .fee-form button:hover {
        background: #0d7a3e;
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
        border-bottom: 1px solid #eef2f7;
        text-align: left;
        vertical-align: top;
    }
    .concessionaire-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
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
</style>
@endsection

@section('content')
    <div class="payments-overview">
        <div class="payments-card">
            <div class="payments-card-top">
                <div class="payments-card-info">
                    <div class="payments-card-label">Total Collected</div>
                    <div class="payments-card-value">₱{{ number_format((float) $totalCollected, 2) }}</div>
                </div>
                <div class="payments-card-icon">₱</div>
            </div>
            <div class="payments-card-note">Filtered payment total</div>
        </div>
        <div class="payments-card">
            <div class="payments-card-top">
                <div class="payments-card-info">
                    <div class="payments-card-label">Records Found</div>
                    <div class="payments-card-value">{{ $payments->total() }}</div>
                </div>
                <div class="payments-card-icon">📋</div>
            </div>
            <div class="payments-card-note">Across all concessionaires</div>
        </div>
        <div class="payments-card">
            <div class="payments-card-top">
                <div class="payments-card-info">
                    <div class="payments-card-label">Overdue This Month</div>
                    <div class="payments-card-value">{{ $overdueCount }}</div>
                </div>
                <div class="payments-card-icon">⚠</div>
            </div>
            <div class="payments-card-note">Active concessionaires with a fee and no payment</div>
        </div>
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
                        <th>Payment Date</th>
                        <th>Recorded By</th>
                        <th>Notes</th>
                        <th>Action</th>
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
                            <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                            <td>{{ $payment->recordedBy?->name ?: '—' }}</td>
                            <td style="max-width:260px;">{{ $payment->notes ?: '—' }}</td>
                            <td>
                                <a href="{{ route('admin.payments.receipt', $payment->id) }}" class="btn btn-outline btn-xs">Download Invoice</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:32px;color:#94a3b8;">No payment records found.</td>
                        </tr>
                    @endforelse
                    <tr id="payments-no-results-row" style="display:none;">
                        <td colspan="8" style="text-align:center;padding:32px;color:#94a3b8;">No results found.</td>
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
</script>
@endsection
