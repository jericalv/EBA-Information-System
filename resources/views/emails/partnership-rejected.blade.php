@component('mail::message')
# EBA Partnership Application - Update Required

Hi {{ $name }},

We reviewed your partnership application, and it was not approved at this time. Please review the reason below and update your information and documents before resubmitting.

Rejection Reason:

{{ $rejectionReason }}

@component('mail::button', ['url' => $resubmitUrl, 'color' => 'success'])
Review and Resubmit
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
