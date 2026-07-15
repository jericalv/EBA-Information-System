@extends('admin.layout')

@section('title', 'Fee Tracking')

@section('extra-css')
<style>
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
    html[data-theme="dark"] .status-badge-overdue { background: rgba(227, 106, 106, 0.14); color: #F0A0A0; }
    html[data-theme="dark"] .status-badge-paid { background: rgba(30, 149, 96, 0.16); color: #8CD6AF; }
    html[data-theme="dark"] .status-badge-due { background: rgba(227, 164, 72, 0.14); color: #E9C288; }
    html[data-theme="dark"] .status-badge-none { background: rgba(255, 255, 255, 0.07); }
    .period-cell {
        white-space: nowrap;
    }
    .period-cell .status-badge {
        margin-left: 6px;
        vertical-align: 1px;
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
        background: var(--field);
        color: var(--ink);
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
        background: var(--hover);
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
</style>
@endsection

@section('content')
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header" style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
            <div>
                <strong style="font-size:16px;color:var(--ink);">Monthly Fee Tracking</strong>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">Set a fee per concessionaire and monitor payment status across the contract period.</div>
            </div>
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                <div style="font-weight:700;color:var(--ink);">{{ $overdueCount }} concessionaire(s) overdue</div>
                <a href="{{ route('admin.payment-logs') }}" class="btn btn-outline">View Payment Logs</a>
            </div>
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
                                $plan = $feePlans[$concessionaire->id] ?? null;

                                $paidThrough = $concessionaire->paid_through_month
                                    ? \Illuminate\Support\Carbon::parse($concessionaire->paid_through_month)->startOfMonth()
                                    : null;
                                $monthsAhead = $paidThrough
                                    ? now()->startOfMonth()->diffInMonths($paidThrough, false)
                                    : 0;

                                $statusLabel = $plan['status_label'] ?? '—';
                                if ($plan && $plan['status'] === 'overdue' && $plan['owed_count'] > 0) {
                                    $statusLabel .= ' · ' . $plan['owed_count'] . ' mo';
                                }
                                $statusClass = 'status-badge-' . ($plan['badge'] ?? 'none');
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
@endsection
