@component('mail::message')
# Concessionaire Account Activated

Hello {{ $application->full_name }},

Your concessionaire account is now active. You can log in and access your concessionaire dashboard.

Access your concessionaire dashboard at:

@component('mail::button', ['url' => 'https://eba.cvsutrece.com/concessionaire', 'color' => 'success'])
Open Concessionaire Dashboard
@endcomponent

Or review your application at:

@component('mail::button', ['url' => route('application'), 'color' => 'success'])
View Application
@endcomponent

Welcome aboard.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
