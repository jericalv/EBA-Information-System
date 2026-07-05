<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 40px 44px; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111827;
            line-height: 1.5;
        }
        .invoice-title {
            font-size: 52px;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: #111827;
            line-height: 1;
        }
        .meta {
            text-align: right;
        }
        .meta-no {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }
        .meta-date {
            font-size: 12px;
            color: #374151;
        }
        .bill-to-label {
            margin-top: 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
        }
        .bill-to {
            font-size: 12px;
            color: #374151;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 48px;
        }
        .items th {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #111827;
            padding: 0 0 10px;
            border-bottom: 1px solid #111827;
        }
        .items td {
            padding: 14px 0;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 12px;
        }
        .col-qty, .col-price, .col-total {
            text-align: right;
            white-space: nowrap;
        }
        .item-name {
            font-weight: 700;
            color: #111827;
        }
        .item-desc {
            color: #6b7280;
            font-size: 11px;
        }
        .totals {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 7px 0;
            font-size: 11px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .totals .t-label {
            text-align: left;
            color: #374151;
        }
        .totals .t-val {
            text-align: right;
            white-space: nowrap;
        }
        .totals .grand td {
            border-top: 1px solid #111827;
            font-weight: 700;
            font-size: 13px;
            color: #111827;
            padding-top: 10px;
        }
        .foot-block {
            font-size: 11px;
            color: #374151;
        }
        .foot-heading {
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #111827;
            margin-bottom: 4px;
        }
        .foot-org {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }
        .foot-right {
            text-align: right;
        }
        .note {
            margin-top: 40px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- Header: INVOICE wordmark (left) + number / date / bill-to (right) --}}
    <table style="width:100%;">
        <tr>
            <td style="vertical-align:top;">
                <div class="invoice-title">INVOICE</div>
            </td>
            <td style="vertical-align:top;" class="meta">
                <div class="meta-no">INVOICE NO: {{ $receiptNumber }}</div>
                <div class="meta-date">{{ ($payment->payment_date ?? $generatedAt)->format('d M Y') }}</div>

                <div class="bill-to-label">BILL TO:</div>
                <div class="bill-to">
                    {{ $payment->concessionaire?->name ?? 'N/A' }}<br>
                    {{ $payment->concessionaire?->business_name ?: 'N/A' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Line items --}}
    <table class="items">
        <thead>
            <tr>
                <th style="text-align:left;">Item</th>
                <th class="col-qty">Period</th>
                <th class="col-price">Rate</th>
                <th class="col-total">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="item-name">Concessionaire Payment</div>
                    <div class="item-desc">
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }} payment{{ $payment->or_number ? ' · OR No. ' . $payment->or_number : '' }}
                        @if (filled($payment->notes))
                            <br>{{ $payment->notes }}
                        @endif
                    </div>
                </td>
                <td class="col-qty">{{ ($payment->payment_date ?? $generatedAt)->format('F Y') }}</td>
                <td class="col-price">PHP {{ number_format((float) $payment->amount, 2) }}</td>
                <td class="col-total">PHP {{ number_format((float) $payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Totals (right aligned) --}}
    <table style="width:100%; margin-top:6px;">
        <tr>
            <td style="width:58%;"></td>
            <td style="width:42%;">
                <table class="totals">
                    <tr>
                        <td class="t-label">Subtotal</td>
                        <td class="t-val">PHP {{ number_format((float) $payment->amount, 2) }}</td>
                    </tr>
                    <tr class="grand">
                        <td class="t-label">Total</td>
                        <td class="t-val">PHP {{ number_format((float) $payment->amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Footer blocks: issuer (left) + payment details (right) --}}
    <table style="width:100%; margin-top:64px;">
        <tr>
            <td style="vertical-align:top;" class="foot-block">
                <div class="foot-org">EBA Information System</div>
                <div>CvSU - Trece Martires City Campus</div>
            </td>
            <td style="vertical-align:top;" class="foot-block foot-right">
                <div class="foot-heading">Payment Details</div>
                <div>Payment Type: {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</div>
                <div>OR Number: {{ $payment->or_number ?: 'N/A' }}</div>
                <div>Payment Date: {{ $payment->payment_date?->format('M d, Y') ?? 'N/A' }}</div>
                <div>Recorded By: {{ $payment->recordedBy?->name ?? 'N/A' }}</div>
                <div>Generated: {{ $generatedAt->format('M d, Y h:i A') }}</div>
            </td>
        </tr>
    </table>

    <div class="note">
        This is an internal invoice record. The Official Receipt is AF No. 51-C issued by the Collecting Officer.
    </div>
</body>
</html>
