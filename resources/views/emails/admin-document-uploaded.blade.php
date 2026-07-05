@component('mail::message')
# Documents Uploaded for Your Application

Your administrator has uploaded documents for your partnership application.

@component('mail::button', ['url' => route('application'), 'color' => 'success'])
View Application Status
@endcomponent

You can view them at: {{ route('application') }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
