<x-mail::message>
# Hello {{ $name }},

Your verification code for **Aqran** is:

<x-mail::panel>
## {{ $code }}
</x-mail::panel>

This code will expire in 10 minutes. If you did not request this code, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
