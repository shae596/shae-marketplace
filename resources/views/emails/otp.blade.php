<h1>Code de vérification SHAE</h1>
<p>Bonjour {{ $user->name }},</p>
<p>Votre code OTP est: <strong>{{ $code }}</strong></p>
<p>Ce code expire dans {{ config('shae.otp_expiration_minutes') }} minutes.</p>
