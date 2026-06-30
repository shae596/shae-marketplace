<h1>Commande confirmée</h1>
<p>Bonjour {{ $user->name }},</p>
<p>Votre commande <strong>{{ $order->reference }}</strong> a été enregistrée.</p>
<p>Total: {{ number_format($order->total, 2) }} $</p>
<p>Procédez au paiement mobile pour finaliser votre achat.</p>
