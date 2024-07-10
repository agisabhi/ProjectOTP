<x-mail::message>
# Introduction

This is your OTP Code

<h2>{{ $maildata['otp'] }}</h2>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
