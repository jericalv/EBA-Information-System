@component('mail::message')
# Payment Received

A payment has been recorded for your concessionaire account.

**Total: ₱{{ number_format($payments->sum(fn ($payment) => (float) $payment->amount), 2) }}**

Received: {{ $receivedDate->format('F d, Y') }}

@component('mail::table')
| Month covered | Amount | OR Number |
| :------------ | :----- | :-------- |
@foreach ($payments as $payment)
| {{ \Illuminate\Support\Carbon::parse($payment->period_month)->format('F Y') }} | ₱{{ number_format((float) $payment->amount, 2) }} | {{ $payment->or_number ?: 'N/A' }} |
@endforeach
@endcomponent

Payment Type: Cash

Recorded By: {{ $payments->first()?->recordedBy?->name ?: 'Cashier' }}

@if ($payments->first()?->notes)
Notes: {{ $payments->first()->notes }}
@endif

@if ($payments->count() > 1)
Download your official invoices below:

@foreach ($payments as $payment)
- [Invoice for {{ \Illuminate\Support\Carbon::parse($payment->period_month)->format('F Y') }} (PDF)]({{ $invoiceUrls[$payment->id] }})
@endforeach
@endif

If you believe there is any discrepancy, please contact the admin office.

@if ($payments->count() === 1)
@component('mail::button', ['url' => $invoiceUrls[$payments->first()->id], 'color' => 'success'])
Download Invoice (PDF)
@endcomponent
@endif

@component('mail::button', ['url' => route('concessionaire.payments')])
View Payment History
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
