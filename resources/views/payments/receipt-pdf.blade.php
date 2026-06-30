<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Reçu {{ $payment->reference }}</title></head>
<body>
    <h2>SHAE — Reçu de paiement</h2>
    <p>Référence: {{ $payment->reference }}</p>
    <p>Commande: {{ $payment->order->reference }}</p>
    <p>Client: {{ $payment->user->name }}</p>
    <p>Montant: {{ number_format($payment->amount, 2) }} $</p>
    <p>Date: {{ $payment->paid_at?->format('d/m/Y H:i') }}</p>
    <p>Statut: {{ $payment->status }}</p>
</body>
</html>
