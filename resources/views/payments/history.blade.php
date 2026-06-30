@extends('layouts.app')

@section('content')
<h1>Historique des paiements</h1>
<table class="table">
    <thead><tr><th>Référence</th><th>Commande</th><th>Montant</th><th>Statut</th><th>Date</th></tr></thead>
    <tbody>
        @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->reference }}</td>
                <td>{{ $payment->order->reference ?? '-' }}</td>
                <td>{{ number_format($payment->amount, 2) }} $</td>
                <td>{{ $payment->status }}</td>
                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $payments->links() }}
@endsection
