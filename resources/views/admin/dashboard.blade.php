@extends('layouts.admin')

@section('sidebar')
<a class="nav-link text-white" href="{{ route('admin.dashboard') }}">Dashboard</a>
<a class="nav-link text-white" href="{{ route('admin.users.index') }}">Utilisateurs</a>
@endsection

@section('content')
<h1>Dashboard Administrateur</h1>
<div class="row g-3 mb-4">
    @foreach($stats as $label => $value)
        <div class="col-md-3">
            <div class="card p-3">
                <small class="text-muted text-uppercase">{{ str_replace('_', ' ', $label) }}</small>
                <h3>{{ is_numeric($value) ? number_format($value, $label === 'revenue' ? 2 : 0) : $value }}</h3>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-md-6">
        <canvas id="salesChart"></canvas>
    </div>
    <div class="col-md-6">
        <h5>Dernières inscriptions</h5>
        <ul class="list-group mb-3">
            @foreach($recentUsers as $user)
                <li class="list-group-item">{{ $user->name }} — {{ $user->email }}</li>
            @endforeach
        </ul>
        <h5>Dernières commandes</h5>
        <ul class="list-group">
            @foreach($recentOrders as $order)
                <li class="list-group-item">{{ $order->reference }} — {{ $order->user->name }} — {{ $order->status }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($monthlySales->toArray())) !!},
        datasets: [{ label: 'Ventes ($)', data: {!! json_encode(array_values($monthlySales->toArray())) !!}, backgroundColor: '#d946ef' }]
    }
});
</script>
@endpush
