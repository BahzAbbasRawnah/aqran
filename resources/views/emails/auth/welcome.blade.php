<x-mail::message>
# Welcome to Aqran, {{ $name }}!

We are excited to have you join our community. Aqran is designed to help you track your habits and achieve your academic goals effectively.

To get started, please log in to the app. You will receive a verification code via email for every login to keep your account secure.

<x-mail::button :url="config('app.url')">
Visit Website
</x-mail::button>

If you have any questions, feel free to reply to this email.

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
