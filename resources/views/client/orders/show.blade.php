@extends('layouts.app')

@section('content')
<h1>Commande {{ $order->reference }}</h1>
<p>Statut: <strong>{{ $order->status }}</strong></p>
<p>Total: {{ number_format($order->total, 2) }} $</p>
<ul>
    @foreach($order->items as $item)
        <li>{{ $item->product->name }} x{{ $item->quantity }} — {{ number_format($item->subtotal, 2) }} $</li>
    @endforeach
</ul>
@if($order->status === 'pending')
    <a href="{{ route('payments.initiate', $order) }}" class="btn btn-shae">Payer maintenant</a>
@endif
@if($order->payment && $order->payment->status === 'success')
    <a href="{{ route('payments.receipt', $order->payment) }}" class="btn btn-outline-shae">Télécharger le reçu</a>
@endif
@endsection
