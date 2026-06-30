@extends('layouts.app')

@section('content')
<h1>Nouveau produit</h1>
<form method="POST" action="{{ route('gestionnaire.products.store') }}" enctype="multipart/form-data" class="card p-4">
    @csrf
    @include('gestionnaire.products._form')
    <button class="btn btn-shae">Enregistrer</button>
</form>
@endsection
