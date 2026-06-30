@extends('layouts.app')

@section('content')
<h1>Modifier produit</h1>
<form method="POST" action="{{ route('gestionnaire.products.update', $product) }}" enctype="multipart/form-data" class="card p-4">
    @csrf
    @include('gestionnaire.products._form', ['product' => $product])
    <button class="btn btn-shae">Mettre à jour</button>
</form>
@endsection
