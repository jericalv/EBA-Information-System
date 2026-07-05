@component('mail::message')
# Contract Expiry Warning

Hello {{ $application->full_name }},

Your concessionaire contract will expire on {{ optional($application->contract_period_end)->format('F d, Y') }} - that is in {{ $days }} {{ $days === 1 ? 'day' : 'days' }}. Please contact admin to renew your contract before it expires.

Visit your concessionaire portal for updates:

@component('mail::button', ['url' => url('/concessionaire'), 'color' => 'success'])
Open Concessionaire Portal
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
