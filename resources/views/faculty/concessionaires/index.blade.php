@extends('faculty.layout')

@section('title', 'Concessionaires')
@section('page-title', 'Concessionaires')

@section('extra-css')
<style>
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
        padding: 9px 12px;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font: inherit;
        color: var(--ink);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .fee-form input:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
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

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #dcfce7;
        color: #166534;
        font-weight: 700;
        font-size: 13px;
    }
    .user-name {
        font-weight: 700;
        color: #0f172a;
    }
    .user-email {
        font-size: 12px;
        color: #64748b;
    }
</style>
@endsection

@section('content')
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
                                    <form method="POST" action="{{ route('staff.concessionaires.monthly-fee', $concessionaire->id) }}" class="fee-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="monthly_fee" min="0" step="0.01" value="{{ $concessionaire->monthly_fee !== null ? number_format((float) $concessionaire->monthly_fee, 2, '.', '') : '' }}" placeholder="0.00" required>
                                        <button type="submit" class="btn btn-green">Set Fee</button>
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
