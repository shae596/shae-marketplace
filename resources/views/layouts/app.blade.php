<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SHAE — Marketplace')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --shae-primary: #d946ef;
            --shae-primary-dark: #a21caf;
            --shae-accent: #ec4899;
            --shae-nav: #701a75;
            --shae-nav-light: #86198f;
            --shae-dark: #1f2937;
            --shae-muted: #6b7280;
            --shae-bg: #f5f3f4;
            --shae-card: #ffffff;
        }
        body { background: var(--shae-bg); color: var(--shae-dark); }
        a { text-decoration: none; }
        .shae-topbar {
            background: linear-gradient(180deg, var(--shae-nav) 0%, var(--shae-nav-light) 100%);
            color: #fff;
            padding: .65rem 0;
        }
        .shae-topbar .navbar-brand {
            font-weight: 900;
            font-size: 1.6rem;
            letter-spacing: 1px;
            color: #fff !important;
            line-height: 1;
        }
        .shae-topbar .navbar-brand span { color: #fbcfe8; }
        .shae-search .form-control {
            border: none;
            border-radius: .5rem 0 0 .5rem;
            padding: .65rem 1rem;
        }
        .shae-search .btn-search {
            background: var(--shae-accent);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 0 .5rem .5rem 0;
            padding: 0 1.25rem;
        }
        .shae-search .btn-search:hover { background: #db2777; color: #fff; }
        .shae-nav-link {
            color: #fce7f3 !important;
            font-size: .85rem;
            line-height: 1.2;
            padding: .35rem .5rem !important;
        }
        .shae-nav-link:hover { color: #fff !important; }
        .shae-nav-link strong { display: block; font-size: .95rem; color: #fff; }
        .shae-category-bar {
            background: #fdf2f8;
            border-bottom: 1px solid #f9a8d4;
            padding: .5rem 0;
        }
        .shae-category-bar .dropdown-toggle {
            background: var(--shae-primary);
            color: #fff;
            border: none;
            font-weight: 600;
            border-radius: .4rem;
            padding: .45rem .85rem;
        }
        .shae-category-bar .dropdown-toggle:hover,
        .shae-category-bar .dropdown-toggle.show {
            background: var(--shae-primary-dark);
            color: #fff;
        }
        .shae-cat-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #f5d0fe;
            color: var(--shae-dark);
            font-size: .85rem;
            font-weight: 500;
            white-space: nowrap;
            transition: all .15s ease;
        }
        .shae-cat-pill:hover, .shae-cat-pill.active {
            background: var(--shae-primary);
            border-color: var(--shae-primary);
            color: #fff;
        }
        .card {
            border: 1px solid #fce7f3;
            border-radius: .75rem;
            background: var(--shae-card);
            box-shadow: 0 2px 8px rgba(217,70,239,.08);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(217,70,239,.15);
        }
        .btn-shae {
            background: linear-gradient(135deg, var(--shae-primary), var(--shae-accent));
            color: #fff;
            border: none;
            border-radius: .5rem;
            font-weight: 600;
        }
        .btn-shae:hover {
            background: linear-gradient(135deg, var(--shae-primary-dark), #db2777);
            color: #fff;
        }
        .btn-outline-shae {
            border: 1px solid var(--shae-primary);
            color: var(--shae-primary-dark);
            background: #fff;
            border-radius: .5rem;
            font-weight: 600;
        }
        .btn-outline-shae:hover {
            background: #fdf2f8;
            color: var(--shae-primary-dark);
            border-color: var(--shae-primary-dark);
        }
        .hero-shae {
            background: linear-gradient(120deg, #701a75 0%, #d946ef 45%, #ec4899 100%);
            color: #fff;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .hero-shae::after {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
        }
        .hero-shae h1 { font-weight: 800; position: relative; z-index: 1; }
        .promo-card {
            background: linear-gradient(145deg, #fdf2f8, #fff);
            border: 1px solid #f5d0fe;
            border-radius: .75rem;
            padding: 1.25rem;
            height: 100%;
        }
        .promo-card h3 { color: var(--shae-primary-dark); font-size: 1rem; font-weight: 700; }
        .category-tile {
            background: #fff;
            border: 1px solid #fce7f3;
            border-radius: .75rem;
            padding: 1rem;
            text-align: center;
            height: 100%;
            transition: all .15s ease;
        }
        .category-tile:hover {
            border-color: var(--shae-primary);
            box-shadow: 0 8px 20px rgba(217,70,239,.12);
            transform: translateY(-2px);
        }
        .category-tile .cat-icon {
            font-size: 2rem;
            line-height: 1;
            margin-bottom: .5rem;
        }
        .category-tile h6 { margin: 0; font-weight: 700; color: var(--shae-dark); font-size: .9rem; }
        .product-placeholder {
            height: 200px;
            background: linear-gradient(135deg, #fdf2f8, #fce7f3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--shae-primary-dark);
            font-weight: 700;
            font-size: .9rem;
        }
        .price-tag { color: var(--shae-primary-dark); font-weight: 800; font-size: 1.15rem; }
        .badge-shae {
            background: #fdf2f8;
            color: var(--shae-primary-dark);
            border: 1px solid #f5d0fe;
        }
        .section-title {
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            padding-bottom: .5rem;
            border-bottom: 3px solid var(--shae-primary);
            display: inline-block;
        }
        .product-card-img { height: 200px; object-fit: cover; }
        .shae-footer {
            background: var(--shae-nav);
            color: #fce7f3;
            margin-top: 3rem;
        }
        .shae-footer strong { color: #fff; }
        .text-shae { color: var(--shae-primary-dark) !important; }
        .pagination .page-link { color: var(--shae-primary-dark); }
        .pagination .page-item.active .page-link {
            background: var(--shae-primary);
            border-color: var(--shae-primary);
        }
    </style>
</head>
<body>
    @include('partials.navbar')
    @include('partials.category-bar')
    <main class="container py-4">
        @include('partials.alerts')
        @yield('content')
    </main>
    <footer class="shae-footer py-4">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-4">
                    <strong>SHAE</strong>
                    <p class="small mb-0 opacity-75">Marketplace congolaise — achetez en ligne, payez via mobile money.</p>
                </div>
                <div class="col-md-4">
                    <strong>Catégories</strong>
                    <ul class="list-unstyled small mb-0">
                        @foreach(($navCategories ?? []) as $cat)
                            <li><a class="text-decoration-none" style="color:#fbcfe8" href="{{ route('home', ['category_id' => $cat->id]) }}">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-4">
                    <strong>Compte</strong>
                    <ul class="list-unstyled small mb-0">
                        <li><a class="text-decoration-none" style="color:#fbcfe8" href="{{ route('login') }}">Connexion</a></li>
                        <li><a class="text-decoration-none" style="color:#fbcfe8" href="{{ route('register') }}">Inscription</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary opacity-25 my-3">
            <small class="opacity-75">&copy; {{ date('Y') }} SHAE · FASI/UPC</small>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
