@component('mail::message')
# Partnership Application Approved

Hello {{ $application->full_name }},

Your partnership application has been approved.

View your application details at:

@component('mail::button', ['url' => route('application'), 'color' => 'success'])
View Application
@endcomponent

The admin team will proceed with concessionaire account activation.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
