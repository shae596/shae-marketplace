@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h1>Catalogue produits</h1>
    <a href="{{ route('gestionnaire.products.create') }}" class="btn btn-shae">Nouveau produit</a>
</div>
<form class="row g-2 mb-3">
    <div class="col-md-4"><input type="text" name="q" class="form-control" placeholder="Rechercher..." value="{{ request('q') }}"></div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Tous les statuts</option>
            @foreach(['pending','approved','rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary">Filtrer</button></div>
</form>
<table class="table">
    <thead><tr><th>Produit</th><th>Responsable</th><th>Prix</th><th>Stock</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->vendor->name }}</td>
                <td>{{ number_format($product->price, 2) }} $</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->status }}</td>
                <td>
                    <a href="{{ route('gestionnaire.products.edit', $product) }}" class="btn btn-sm btn-primary">Modifier</a>
                    <form action="{{ route('gestionnaire.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Supprimer</button></form>
                    <form action="{{ route('gestionnaire.products.status', $product) }}" method="POST" class="d-inline-flex gap-1 mt-1">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select form-select-sm">
                            <option value="approved">Approuver</option>
                            <option value="rejected">Rejeter</option>
                            <option value="pending">En attente</option>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary">Statut</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $products->links() }}
@endsection
