@php
    $categoryIcons = [
        'electronique' => '📱',
        'électronique' => '📱',
        'mode' => '👗',
        'maison' => '🏠',
        'alimentation' => '🍽️',
        'services' => '🔧',
    ];
@endphp
<header class="shae-topbar">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center gap-2 gap-lg-3">
            <a class="navbar-brand me-lg-3" href="{{ route('home') }}">SH<span>AE</span></a>

            <form method="GET" action="{{ route('home') }}" class="shae-search flex-grow-1 d-flex" style="max-width:640px">
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                <input type="text" name="q" class="form-control" placeholder="Rechercher sur SHAE..." value="{{ request('q') }}" aria-label="Rechercher">
                <button class="btn btn-search" type="submit">🔍</button>
            </form>

            <div class="d-flex align-items-center gap-1 ms-lg-auto">
                @auth
                    <a class="shae-nav-link" href="{{ route('profile.edit') }}">
                        Bonjour<br><strong>{{ Str::before(auth()->user()->name, ' ') }}</strong>
                    </a>
                    <a class="shae-nav-link" href="{{ route('orders.index') }}">
                        Retours<br><strong>Commandes</strong>
                    </a>
                    <a class="shae-nav-link position-relative" href="{{ route('cart.index') }}">
                        Panier<br><strong>🛒</strong>
                    </a>
                    @if(auth()->user()->hasRole('admin'))
                        <a class="shae-nav-link" href="{{ route('admin.dashboard') }}">Admin<br><strong>Panel</strong></a>
                    @endif
                    @if(auth()->user()->hasRole('gestionnaire','admin'))
                        <a class="shae-nav-link" href="{{ route('gestionnaire.dashboard') }}">Gestion<br><strong>Catalogue</strong></a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-link shae-nav-link text-decoration-none border-0 p-1">Sortir<br><strong>Déconnexion</strong></button>
                    </form>
                @else
                    <a class="shae-nav-link" href="{{ route('login') }}">
                        Identifiez-vous<br><strong>Compte</strong>
                    </a>
                    <a class="shae-nav-link" href="{{ route('register') }}">
                        Nouveau<br><strong>Client</strong>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
