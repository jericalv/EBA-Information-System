@component('mail::message')
# Your EBA Staff Account Has Been Created

Hello {{ $user->name }}, your staff account has been created.

Email: {{ $user->email }}

Temporary Password: {{ $temporaryPassword }}

Login URL: {{ route('login') }}

Please change your password after your first login.

If you have any questions, please contact the admin office.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
