@component('mail::message')
# Halo!

Terima kasih sudah mendaftar di **{{ config('app.name') }}**.

Silakan klik tombol di bawah ini untuk memverifikasi email Anda:

@component('mail::button', ['url' => $verificationUrl])
Verifikasi Email
@endcomponent

Jika Anda tidak membuat akun, abaikan email ini.

Salam hangat,  
{{ config('app.name') }}
@endcomponent