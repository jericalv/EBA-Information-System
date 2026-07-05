@component('mail::message')
# Documents Received

Hello {{ $application->full_name }},

We received your uploaded concessionaire documents. Your application is now under review by faculty/staff.

Track your application status here:

@component('mail::button', ['url' => route('application'), 'color' => 'success'])
Track Application Status
@endcomponent

Business: {{ $application->business_name }}

Thank you for your patience.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
