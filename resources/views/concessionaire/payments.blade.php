@extends('concessionaire.layout')

@section('title', 'Payments')

@section('extra-css')
<style>
    .payments-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .payment-banner {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .payment-banner svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .payment-banner-title {
        font-weight: 700;
        font-size: 13.5px;
    }
    .payment-banner-text {
        font-size: 13px;
        margin-top: 2px;
        opacity: 0.85;
    }
    .payments-table-wrap {
        overflow-x: auto;
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 12px;
        box-shadow: var(--shadow-card);
    }
    .payments-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }
    .payments-table thead th {
        text-align: left;
        padding: 11px 18px;
        background: var(--paper);
        color: var(--muted);
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        border-bottom: 1px solid var(--line);
    }
    .payments-table tbody td {
        padding: 13px 18px;
        border-bottom: 1px solid var(--line);
        vertical-align: top;
        font-size: 13.5px;
        color: var(--ink);
    }
    .payments-table tbody tr:hover {
        background: var(--hover);
    }
    .payments-table tbody tr:last-child td {
        border-bottom: none;
    }
    .payment-amount {
        font-family: var(--font-mono);
        font-weight: 600;
        color: var(--pine);
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .payment-empty {
        background: var(--card);
        border: 1px dashed var(--line-strong);
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        color: var(--muted);
        font-size: 13.5px;
    }
    .payment-tag {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 5px;
        border: 1px solid var(--line);
        background: var(--paper);
        color: var(--ink);
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="payments-page">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if($hasOverduePayment ?? false)
        <div class="alert alert-error payment-banner">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div>
                <div class="payment-banner-title">Payment overdue</div>
                <div class="payment-banner-text">
                    You have {{ ($feePlan['owed_count'] ?? 0) > 0 ? $feePlan['owed_count'] . ' unpaid month(s)' : 'an unpaid balance' }} from previous months. Please coordinate with the cashier as soon as possible.
                </div>
            </div>
        </div>
    @elseif($hasPaidThisMonth ?? false)
        <div class="alert alert-success payment-banner">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <div>
                <div class="payment-banner-title">Payment recorded this month</div>
                <div class="payment-banner-text">Your monthly fee has been recorded. You're all good for this month.</div>
            </div>
        </div>
    @elseif($isDueSoon ?? false)
        <div class="alert alert-warning payment-banner">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <div class="payment-banner-title">Payment due this month</div>
                <div class="payment-banner-text">Your monthly fee for <strong>{{ now()->format('F Y') }}</strong> hasn't been recorded yet. Please coordinate with the cashier before the end of the month.</div>
            </div>
        </div>
    @endif

    <section class="stat-ledger" aria-label="Payment statistics">
        <article class="stat-cell">
            <span class="eyebrow">Total paid</span>
            <div class="stat-value-row">
                <span class="stat-value">&#8369;{{ number_format((float) $totalPaid, 2) }}</span>
            </div>
            <span class="stat-foot">All recorded payments</span>
        </article>
        <article class="stat-cell">
            <span class="eyebrow">Monthly fee</span>
            <div class="stat-value-row">
                <span class="stat-value">{{ $user->monthly_fee && $user->monthly_fee > 0 ? '₱' . number_format((float) $user->monthly_fee, 2) : '—' }}</span>
            </div>
            <span class="stat-foot">Set by admin</span>
        </article>
        <article class="stat-cell">
            <span class="eyebrow">Last payment</span>
            <div class="stat-value-row">
                <span class="stat-value stat-value-sm">{{ $lastPaymentDate ? $lastPaymentDate->format('M d, Y') : '—' }}</span>
            </div>
            <span class="stat-foot">Most recent recorded date</span>
        </article>
        <article class="stat-cell">
            <span class="eyebrow">Contract period</span>
            <div class="stat-value-row">
                <span class="stat-value stat-value-sm">
                    {{ $latestApplication?->contract_period_start ? $latestApplication->contract_period_start->format('M d, Y') : '—' }}
                    <br>
                    <span class="stat-unit">to {{ $latestApplication?->contract_period_end ? $latestApplication->contract_period_end->format('M d, Y') : '—' }}</span>
                </span>
            </div>
            <span class="stat-foot">Latest partnership application</span>
        </article>
    </section>

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
                            <td><span class="payment-amount">&#8369;{{ number_format((float) $payment->amount, 2) }}</span></td>
                            <td><span class="payment-tag">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</span></td>
                            <td>{{ $payment->or_number ?: '—' }}</td>
                            <td style="max-width:260px;">{{ $payment->notes ?: '—' }}</td>
                            <td>{{ $payment->recordedBy?->name ?: '—' }}</td>
                            <td>
                                <a class="btn btn-secondary btn-sm" href="{{ route('concessionaire.payments.receipt', $payment) }}">Download Receipt</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
