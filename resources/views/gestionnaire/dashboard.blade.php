@extends('layouts.app')

@section('title', 'Espace gestionnaire — SHAE')

@section('content')
<h1 class="section-title">Espace gestionnaire</h1>
<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Produits', 'value' => $stats['products_count']],
        ['label' => 'Approuvés', 'value' => $stats['approved_products']],
        ['label' => 'En attente', 'value' => $stats['pending_products']],
        ['label' => 'Commandes en cours', 'value' => $stats['orders_processing']],
    ] as $stat)
        <div class="col-6 col-md-3">
            <div class="card p-3 h-100" style="border-left:4px solid var(--shae-primary)">
                <small class="text-muted text-uppercase">{{ $stat['label'] }}</small>
                <h3 class="text-shae mb-0">{{ $stat['value'] }}</h3>
            </div>
        </div>
    @endforeach
</div>
<div class="card p-4 mb-4">
    <p class="mb-1">Ventes enregistrées: <strong>{{ $stats['sales_count'] }}</strong></p>
    <p class="mb-0 price-tag">Revenus: {{ number_format($stats['revenue'], 2) }} $</p>
</div>
<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('gestionnaire.products.index') }}" class="btn btn-shae">Gérer le catalogue</a>
    <a href="{{ route('gestionnaire.products.create') }}" class="btn btn-outline-shae">Nouveau produit</a>
</div>
@endsection
