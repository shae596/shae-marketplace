<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administration SHAE')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <aside class="bg-dark text-white p-3" style="min-width:220px; min-height:100vh;">
            <h5 class="mb-4">SHAE Admin</h5>
            @yield('sidebar')
        </aside>
        <main class="flex-grow-1 p-4">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
