@component('mail::message')
# New Partnership Application Submitted

A new partnership application has been submitted.

Applicant: {{ $application->full_name }}

Business: {{ $application->business_name }}

Email: {{ $application->email }}

@component('mail::button', ['url' => 'https://eba.cvsutrece.com/admin/partnerships', 'color' => 'success'])
Review Application
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
