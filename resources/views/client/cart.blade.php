@extends('layouts.app')

@section('title', 'Mon panier — SHAE')

@section('content')
<h1 class="section-title">Mon panier</h1>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light"><tr><th>Produit</th><th>Qté</th><th>Prix</th><th>Total</th><th></th></tr></thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    <br><small class="text-muted">{{ $product->category->name }}</small>
                                </td>
                                <td>{{ $cart[$product->id] }}</td>
                                <td>{{ number_format($product->price, 2) }} $</td>
                                <td class="price-tag">{{ number_format($product->price * $cart[$product->id], 2) }} $</td>
                                <td>
                                    <form action="{{ route('cart.remove', $product) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Retirer</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Votre panier est vide.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Récapitulatif</h5>
            <div class="d-flex justify-content-between mb-2"><span>Sous-total</span><strong>{{ number_format($total, 2) }} $</strong></div>
            <div class="d-flex justify-content-between mb-3 text-muted small"><span>Livraison</span><span>Calculée à l'étape suivante</span></div>
            <hr>
            <div class="d-flex justify-content-between mb-4"><span class="fw-bold">Total</span><span class="price-tag">{{ number_format($total, 2) }} $</span></div>
            @if($products->count())
                <a href="{{ route('checkout') }}" class="btn btn-shae w-100 py-2">Passer la commande</a>
            @else
                <a href="{{ route('home') }}" class="btn btn-outline-shae w-100">Continuer mes achats</a>
            @endif
        </div>
    </div>
</div>
@endsection
