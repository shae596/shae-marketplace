@extends('layouts.app')

@section('content')
<h1>Paiement mobile</h1>
<p>Commande: {{ $order->reference }}</p>
<p>Montant: <strong>{{ number_format($order->total, 2) }} $</strong></p>
<form method="POST" action="{{ route('payments.process', $order) }}" class="card p-4">
    @csrf
    <div class="mb-3">
        <label class="form-label">Numéro mobile money</label>
        <input type="text" name="phone" class="form-control" placeholder="0891234567" value="{{ old('phone') }}" required>
        <div class="form-text">Numéro Mobile Money RDC — 10 chiffres (M-Pesa, Airtel, Orange). Vous recevrez un push USSD pour saisir votre code PIN.</div>
    </div>
    <button class="btn btn-shae">Initier le paiement LabPay</button>
</form>
@endsection
