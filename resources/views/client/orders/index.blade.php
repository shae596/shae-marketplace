@extends('layouts.app')

@section('content')
<h1>Mes commandes</h1>
<table class="table">
    <thead><tr><th>Référence</th><th>Total</th><th>Statut</th><th>Date</th><th></th></tr></thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->reference }}</td>
                <td>{{ number_format($order->total, 2) }} $</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td><a href="{{ route('orders.show', $order) }}">Détails</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $orders->links() }}
@endsection
