@component('mail::message')
# Partnership Application Received

Hello {{ $application->full_name }},

Your partnership application has been received and is under review.

You can track your application status anytime by visiting:

@component('mail::button', ['url' => route('application'), 'color' => 'success'])
Track Application Status
@endcomponent

Log in with your registered account to view your application status, uploaded documents, and any updates from our team.

Business: {{ $application->business_name }}

Thank you for your interest in becoming a concessionaire partner.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
