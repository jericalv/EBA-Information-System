@component('mail::message')
# Payment Due Reminder - {{ $dueDate }}

Hi {{ $name }},

This is a reminder that your monthly concessionaire fee of {{ $monthlyFee }} for {{ $dueDate }} is still outstanding.

Please coordinate with the EBA cashier to settle your payment before the end of the month to avoid an overdue status.

You can view your payment history here:

@component('mail::button', ['url' => $paymentsUrl, 'color' => 'success'])
View Payments
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent