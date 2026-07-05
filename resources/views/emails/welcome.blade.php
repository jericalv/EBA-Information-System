@component('mail::message')
# Welcome to EBA Information System

Welcome {{ $user->name }}! Your account has been created.

You can now log in and start using the system.

@component('mail::button', ['url' => 'https://eba.cvsutrece.com/login', 'color' => 'success'])
Log In
@endcomponent

Login at: https://eba.cvsutrece.com/login

This is an automated message from EBA Information System. Please do not reply to this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
