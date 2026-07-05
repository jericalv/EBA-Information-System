@extends('concessionaire.layout')

@section('title', 'Payments')

@section('extra-css')
<style>
    .payments-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .payments-header h1 {
        font-size: 28px;
        font-weight: 800;
        color: var(--green);
        margin-bottom: 8px;
    }
    .payments-header p {
        color: #64748b;
        font-size: 14px;
    }
    .payments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .payment-stat {
        background: #fff;
        border: 1px solid rgba(10,92,47,0.08);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    }
    .payment-stat-label {
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
    }
    .payment-stat-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--green);
    }
    .payment-stat-note {
        margin-top: 6px;
        font-size: 12px;
        color: #64748b;
    }
    .payments-table-wrap {
        overflow-x: auto;
        background: #fff;
        border: 1px solid rgba(10,92,47,0.08);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    }
    .payments-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }
    .payments-table thead th {
        text-align: left;
        padding: 14px 18px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1px solid #e2e8f0;
    }
    .payments-table tbody td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
        font-size: 14px;
        color: #334155;
    }
    .payments-table tbody tr:last-child td {
        border-bottom: none;
    }
    .payment-empty {
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        color: #64748b;
    }
    .payment-tag {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(10,92,47,0.08);
        color: var(--green);
        font-size: 12px;
        font-weight: 700;
    }
    .receipt-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(10,92,47,0.2);
        background: #fff;
        color: var(--green);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }
    .receipt-button:hover {
        background: #f0fdf4;
    }
</style>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if($hasPaidThisMonth ?? false)
        <div style="display:flex;align-items:center;gap:14px;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:14px;padding:16px 22px;margin-bottom:24px;">
            <div style="width:40px;height:40px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;color:#16a34a;" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div style="font-weight:800;font-size:15px;color:#15803d;">Payment Recorded This Month</div>
                <div style="font-size:13px;color:#166534;margin-top:2px;">Your monthly fee has been recorded. You're all good for this month!</div>
            </div>
        </div>

    @elseif($isDueSoon ?? false)
        <div style="display:flex;align-items:center;gap:14px;background:#fffbeb;border:1.5px solid #fde68a;border-radius:14px;padding:16px 22px;margin-bottom:24px;">
            <div style="width:40px;height:40px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-weight:800;font-size:15px;color:#b45309;">Payment Due Soon</div>
                <div style="font-size:13px;color:#92400e;margin-top:2px;">Your monthly fee is due by the <strong>1st of this month</strong>. Please coordinate with the cashier before the deadline.</div>
            </div>
        </div>

    @elseif($hasOverduePayment ?? false)
        <div style="display:flex;align-items:center;gap:14px;background:#fff1f2;border:1.5px solid #fecdd3;border-radius:14px;padding:16px 22px;margin-bottom:24px;">
            <div style="width:40px;height:40px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </div>
            <div>
                <div style="font-weight:800;font-size:15px;color:#b91c1c;">Payment Overdue</div>
                <div style="font-size:13px;color:#991b1b;margin-top:2px;">Your monthly fee is past due. Please coordinate with the cashier as soon as possible.</div>
            </div>
        </div>
    @endif

    <div class="payments-grid">
        <div class="payment-stat">
            <div class="payment-stat-label">Total Paid</div>
            <div class="payment-stat-value">₱{{ number_format((float) $totalPaid, 2) }}</div>
            <div class="payment-stat-note">All recorded payments</div>
        </div>
        <div class="payment-stat">
            <div class="payment-stat-label">Monthly Fee</div>
            <div class="payment-stat-value">
                {{ $user->monthly_fee && $user->monthly_fee > 0 ? '₱' . number_format((float) $user->monthly_fee, 2) : '—' }}
            </div>
            <div class="payment-stat-note">Set by admin</div>
        </div>
        <div class="payment-stat">
            <div class="payment-stat-label">Last Payment</div>
            <div class="payment-stat-value">{{ $lastPaymentDate ? $lastPaymentDate->format('M d, Y') : '—' }}</div>
            <div class="payment-stat-note">Most recent recorded date</div>
        </div>
        <div class="payment-stat">
            <div class="payment-stat-label">Contract Period</div>
            <div class="payment-stat-value" style="font-size:18px;line-height:1.4;">
                {{ $latestApplication?->contract_period_start ? $latestApplication->contract_period_start->format('M d, Y') : '—' }}
                <br>
                <span style="font-size:14px;font-weight:700;color:#64748b;">to {{ $latestApplication?->contract_period_end ? $latestApplication->contract_period_end->format('M d, Y') : '—' }}</span>
            </div>
            <div class="payment-stat-note">Latest partnership application</div>
        </div>
    </div>

    @if ($payments->isEmpty())
        <div class="payment-empty">
            No payments have been recorded yet.
        </div>
    @else
        <div class="payments-table-wrap">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Type</th>
                        <th>OR Number</th>
                        <th>Notes</th>
                        <th>Recorded By</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                            <td style="font-weight:800;color:var(--green);">₱{{ number_format((float) $payment->amount, 2) }}</td>
                            <td><span class="payment-tag">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</span></td>
                            <td>{{ $payment->or_number ?: '—' }}</td>
                            <td style="max-width:260px;">{{ $payment->notes ?: '—' }}</td>
                            <td>{{ $payment->recordedBy?->name ?: '—' }}</td>
                            <td>
                                <a class="receipt-button" href="{{ route('concessionaire.payments.receipt', $payment) }}">Download Receipt</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px;">
            {{ $payments->links() }}
        </div>
    @endif
@endsection
