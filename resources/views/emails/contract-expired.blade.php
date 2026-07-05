@component('mail::message')
# Concessionaire Contract Expired

Hello {{ $application->full_name }},

Your concessionaire contract has expired as of {{ optional($application->contract_period_end)->format('F d, Y') }}. Your account has been moved back to pending concessionaire status until renewal is approved. Please contact admin to renew your contract.

You can review your latest partnership status here:

@component('mail::button', ['url' => route('application'), 'color' => 'success'])
Open Partnership Status
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
