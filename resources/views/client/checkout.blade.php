@extends('layouts.app')

@section('content')
<h1>Finaliser la commande</h1>
<form method="POST" action="{{ route('orders.store') }}" class="card p-4">
    @csrf
    <div class="mb-3">
        <label class="form-label">Adresse de livraison</label>
        <input type="text" name="shipping_address" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="text" name="shipping_phone" class="form-control" required>
    </div>
    <button class="btn btn-shae">Confirmer la commande</button>
</form>
@endsection
