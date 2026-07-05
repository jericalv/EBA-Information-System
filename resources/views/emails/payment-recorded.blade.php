@component('mail::message')
# Payment Received

A payment has been recorded for your concessionaire account.

Amount: ₱{{ number_format((float) $payment->amount, 2) }}

Payment Date: {{ $payment->payment_date?->format('F d, Y') }}

Payment Type: {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}

OR Number: {{ $payment->or_number ?: 'N/A' }}

Recorded By: {{ $payment->recordedBy?->name ?: 'Cashier' }}

@if ($payment->notes)
Notes: {{ $payment->notes }}
@endif

If you believe there is any discrepancy, please contact the admin office.

@component('mail::button', ['url' => route('concessionaire.payments'), 'color' => 'success'])
View Payment History
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
