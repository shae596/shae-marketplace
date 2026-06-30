@extends('layouts.app')

@section('content')
<h1>Statut du paiement</h1>
<p>Référence: <strong>{{ $payment->reference }}</strong></p>
<p>Statut: <strong class="text-capitalize">{{ $payment->status === 'success' ? 'Payé' : $payment->status }}</strong></p>
<p>Montant: {{ number_format($payment->amount, 2) }} $</p>
<p>Mobile money: {{ $payment->phone }}</p>

@if($payment->status === 'pending')
    @if(empty(config('shae.labpay.api_key')))
        <div class="alert alert-info">
            <strong>Mode simulation (local)</strong> — Aucun SMS n’est envoyé sur votre téléphone et aucun argent n’est débité.
            Cliquez ci-dessous pour marquer le paiement comme réussi (test).
            La confirmation arrive par <strong>email</strong> (Mailtrap en dev).
        </div>
        <form action="{{ route('payments.simulate', $payment) }}" method="POST" class="mt-2">
            @csrf
            <button class="btn btn-shae">Simuler paiement réussi (dev)</button>
        </form>
    @else
        <div class="alert alert-warning">
            <strong>Paiement réel LabPay</strong><br>
            1. Un push USSD / message apparaît sur <strong>{{ $payment->phone }}</strong>.<br>
            2. Entrez votre <strong>code PIN</strong> mobile money (M-Pesa, Airtel ou Orange).<br>
            3. Cette page se met à jour automatiquement quand LabPay confirme le paiement.<br>
            @if(data_get($payment->provider_response, 'callback_url_used'))
                <span class="small d-block mt-2 text-muted">Callback LabPay : {{ data_get($payment->provider_response, 'callback_url_used') }}</span>
            @endif
        </div>
        <meta http-equiv="refresh" content="5">
        <form action="{{ route('payments.confirm', $payment) }}" method="POST" class="mt-3">
            @csrf
            <button class="btn btn-outline-shae" onclick="return confirm('Confirmez uniquement si l\'argent a bien été débité sur votre téléphone.')">
                J'ai payé sur mon téléphone — confirmer le paiement
            </button>
        </form>
        <p class="small text-muted mt-2 mb-0">Si le débit a réussi mais la page reste en attente, utilisez le bouton ci-dessus (secours si le callback LabPay n’arrive pas).</p>
    @endif
@endif

@if($payment->status === 'success')
    <div class="alert alert-success">Paiement confirmé. Consultez Mailtrap pour le reçu email (mode dev).</div>
    <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-shae">Télécharger le reçu PDF</a>
    <a href="{{ route('orders.show', $payment->order) }}" class="btn btn-outline-shae ms-2">Voir la commande</a>
@endif
@endsection
