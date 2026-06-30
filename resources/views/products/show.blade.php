@extends('layouts.app')

@section('title', $product->name.' — SHAE')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-shae">Accueil</a></li>
        <li class="breadcrumb-item">
            <a href="{{ route('home', ['category_id' => $product->category_id]) }}" class="text-shae">
                {{ $product->category->name }}
            </a>
        </li>
        <li class="breadcrumb-item active">{{ Str::limit($product->name, 40) }}</li>
    </ol>
</nav>

<div class="card overflow-hidden">
    <div class="row g-0">
        <div class="col-md-5 bg-white p-3 p-md-4 text-center">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}" style="max-height:380px;object-fit:contain">
            @else
                <div class="product-placeholder rounded" style="min-height:320px">{{ $product->category->name }}</div>
            @endif
        </div>
        <div class="col-md-7 p-4">
            <span class="badge badge-shae">{{ $product->category->name }}</span>
            <h1 class="h3 mt-2 mb-3">{{ $product->name }}</h1>
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="text-warning">★★★★☆</span>
                <span class="text-muted small">Vendeur SHAE · {{ $product->vendor->name }}</span>
            </div>
            <p class="price-tag display-6 mb-3">{{ number_format($product->price, 2) }} $</p>
            <p class="text-muted">{{ $product->description }}</p>
            <ul class="list-unstyled small mb-4">
                <li>✓ Livraison disponible à Kinshasa</li>
                <li>✓ Paiement mobile money (LabPay)</li>
                <li>✓ Stock : <strong>{{ $product->stock }}</strong> unité(s)</li>
            </ul>
            <div class="d-flex flex-wrap gap-2">
                @auth
                    @if(auth()->user()->hasRole('client'))
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <button class="btn btn-shae btn-lg px-4">Ajouter au panier</button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-shae btn-lg px-4">Connectez-vous pour acheter</a>
                @endauth
                <a href="{{ route('home', ['category_id' => $product->category_id]) }}" class="btn btn-outline-shae">Plus dans cette catégorie</a>
            </div>
        </div>
    </div>
</div>
@endsection
