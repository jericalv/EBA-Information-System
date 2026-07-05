@component('mail::message')
# Contract Period Set for Your Concessionaire Account

Your contract period has been set by the admin.

Contract Start: {{ optional($application->contract_period_start)->format('F d, Y') }}

Contract End: {{ optional($application->contract_period_end)->format('F d, Y') }}

@component('mail::button', ['url' => route('application'), 'color' => 'success'])
Track Application
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
