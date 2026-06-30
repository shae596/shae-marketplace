@extends('layouts.app')

@section('title', 'SHAE — Marketplace')

@section('content')
@php
    $categoryTiles = [
        'Électronique' => ['icon' => '📱', 'desc' => 'Smartphones, casques, accessoires'],
        'Mode' => ['icon' => '👗', 'desc' => 'Vêtements, chaussures, accessoires'],
        'Maison' => ['icon' => '🏠', 'desc' => 'Décoration, cuisine, bricolage'],
        'Alimentation' => ['icon' => '🍳', 'desc' => 'Riz, huile et produits locaux'],
        'Beauté' => ['icon' => '💄', 'desc' => 'Soins, parfums, cosmétiques'],
    ];
@endphp

@if(!($activeCategory ?? null) && !request('q'))
<section class="hero-shae mb-4">
    <div class="row align-items-center position-relative" style="z-index:1">
        <div class="col-lg-7">
            <span class="badge rounded-pill mb-2" style="background:rgba(255,255,255,.2)">Paiement mobile LabPay</span>
            <h1 class="display-6 mb-2">SHAE — votre marketplace en ligne</h1>
            <p class="mb-3 opacity-90">Mode, électronique, maison, beauté et plus — payez via mobile money.</p>
            <a href="#catalogue" class="btn btn-light fw-bold text-shae">Découvrir le catalogue</a>
        </div>
        <div class="col-lg-5 mt-3 mt-lg-0">
            <div class="row g-2">
                <div class="col-6"><div class="promo-card"><h3>🔥 Offres</h3><p class="small text-muted mb-0">Promos mode & tech</p></div></div>
                <div class="col-6"><div class="promo-card"><h3>📦 Livraison</h3><p class="small text-muted mb-0">Express disponible</p></div></div>
                <div class="col-12"><div class="promo-card"><h3>💳 Mobile Money</h3><p class="small text-muted mb-0">Paiement LabPay</p></div></div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <h2 class="section-title">Acheter par catégorie</h2>
    <div class="row g-3">
        @foreach($navCategories ?? [] as $category)
            @php $tile = $categoryTiles[$category->name] ?? ['icon' => '🏷️', 'desc' => 'Explorer']; @endphp
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('home', ['category_id' => $category->id]) }}" class="text-decoration-none d-block h-100">
                    <div class="category-tile">
                        <div class="cat-icon">{{ $tile['icon'] }}</div>
                        <h6>{{ $category->name }}</h6>
                        <p class="small text-muted mb-0">{{ $tile['desc'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</section>
@endif

<div id="catalogue" class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        @if($activeCategory ?? null)
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-shae">Accueil</a></li>
                    <li class="breadcrumb-item active">{{ $activeCategory->name }}</li>
                </ol>
            </nav>
            <h2 class="section-title mb-0">{{ $activeCategory->name }}</h2>
        @elseif(request('q'))
            <h2 class="section-title mb-0">Résultats pour « {{ request('q') }} »</h2>
        @else
            <h2 class="section-title mb-0">Recommandés pour vous</h2>
        @endif
        <p class="text-muted small mb-0">{{ $products->total() }} produit(s)</p>
    </div>
</div>

<div class="row g-3 mb-4">
    @forelse($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 product-card">
                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top product-card-img" alt="{{ $product->name }}">
                    @else
                        <div class="product-placeholder">{{ Str::limit($product->category->name, 12) }}</div>
                    @endif
                </a>
                <div class="card-body d-flex flex-column p-3">
                    <span class="badge badge-shae align-self-start mb-2">{{ $product->category->name }}</span>
                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                        <h6 class="card-title mb-1" style="font-size:.9rem;line-height:1.35">{{ Str::limit($product->name, 48) }}</h6>
                    </a>
                    <p class="price-tag mb-2">{{ number_format($product->price, 2) }} $</p>
                    @if($product->stock > 0)
                        <small class="text-success mb-2">En stock</small>
                    @endif
                    <div class="mt-auto d-grid gap-1">
                        @auth
                            @if(auth()->user()->hasRole('client'))
                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-shae btn-sm w-100">Ajouter au panier</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-shae btn-sm">Acheter maintenant</a>
                        @endauth
                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-shae btn-sm">Voir le produit</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center">
                <p class="h5 text-shae mb-2">Aucun produit trouvé</p>
                <a href="{{ route('home') }}" class="btn btn-shae">Voir tout le catalogue</a>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center">{{ $products->links() }}</div>
@endsection
