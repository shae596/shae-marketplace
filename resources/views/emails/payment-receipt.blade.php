<h1>Reçu de paiement SHAE</h1>
<p>Bonjour {{ $user->name }},</p>
<p>Paiement confirmé — Référence: <strong>{{ $payment->reference }}</strong></p>
<p>Montant: {{ number_format($payment->amount, 2) }} $</p>
<p>Merci pour votre confiance.</p>
