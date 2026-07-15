<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 24px 26px; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111827;
            line-height: 1.45;
        }
        .sheet {
            border: 1px solid #d1d5db;
            padding: 16px;
        }
        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .subtitle {
            font-size: 12px;
            color: #374151;
            margin-bottom: 8px;
        }
        .meta {
            font-size: 11px;
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th,
        td {
            border: 1px solid #d1d5db;
            padding: 7px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .amount {
            font-weight: 700;
            white-space: nowrap;
        }
        .muted {
            color: #6b7280;
        }
        .badge {
            display: inline-block;
            padding: 1px 7px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .badge-advance {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }
        .badge-ontime {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }
        .badge-late {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <div class="title">EBA Information System</div>
            <div class="subtitle">Cashier Payment Logs Report{{ !empty($periodLabel) ? ' — ' . $periodLabel : '' }}</div>
            <div class="meta">Generated: {{ $generatedAt->format('M d, Y h:i A') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Concessionaire</th>
                    <th>Amount Paid (PHP)</th>
                    <th>Payment Date</th>
                    <th>Period Covered</th>
                    <th>Payment Status</th>
                    <th>Payment Type</th>
                    <th>OR Number</th>
                    <th>Recorded By</th>
                    <th>Recorded At</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $badgeClasses = ['Advance' => 'badge-advance', 'On Time' => 'badge-ontime', 'Late' => 'badge-late'];
                @endphp
                @forelse ($payments as $payment)
                    @php $timing = $payment->paymentTiming(); @endphp
                    <tr>
                        <td>{{ $payment->concessionaire?->business_name ?: $payment->concessionaire?->name ?: 'N/A' }}</td>
                        <td class="amount">PHP {{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td>{{ $payment->period_month?->format('M Y') ?? 'N/A' }}</td>
                        <td>
                            @if ($timing)
                                <span class="badge {{ $badgeClasses[$timing] ?? '' }}">{{ $timing }}</span>
                            @else
                                <span class="muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', (string) $payment->payment_type)) }}</td>
                        <td>{{ $payment->or_number ?: 'N/A' }}</td>
                        <td>{{ $payment->recordedBy?->name ?: 'N/A' }}</td>
                        <td>{{ $payment->created_at?->setTimezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="muted">No payment records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
