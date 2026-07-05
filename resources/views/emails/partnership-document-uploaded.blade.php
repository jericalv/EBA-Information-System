@component('mail::message')
# New Partnership Document Uploaded

{{ $application->full_name }} has uploaded a new document for their partnership application.

Application ID: {{ $application->id }}

Email: {{ $application->email }}

@if($application->letter_of_intent)
@component('mail::button', ['url' => asset('storage/' . $application->letter_of_intent), 'color' => 'success'])
View Uploaded File
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
