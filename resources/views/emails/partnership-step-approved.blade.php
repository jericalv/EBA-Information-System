@component('mail::message')
# {{ $heading }}

Hello {{ $application->full_name }},

Good news — **{{ $stepLabel }}** of your concessionaire partnership application has been approved by the EBA Office. ✅

{{ $body }}

@component('mail::panel')
**Next step:** {{ $nextStep }}
@endcomponent

@component('mail::button', ['url' => route('application'), 'color' => 'success'])
Continue Your Application
@endcomponent

If you have any questions, simply reply to this email and our team will be glad to help.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
