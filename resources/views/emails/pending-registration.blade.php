@component('mail::message')
# Confirm Your Registration

Thank you for registering with the EBA Information System. Click the button below to confirm your email address and create your account. This link will expire in 24 hours.

@component('mail::button', ['url' => $confirmUrl])
Confirm Email Address
@endcomponent

If you did not register, no action is required.

Thanks,<br>
EBA Information System
@endcomponent
