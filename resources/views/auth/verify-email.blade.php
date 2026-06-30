@extends('layouts.app')

@section('content')
<div class="card p-4">
    <h2 class="h4">Vérifiez votre adresse email</h2>
    <p>Un lien de confirmation a été envoyé à votre adresse email.</p>
    @if(app()->isLocal())
        <p class="text-muted small mb-3">En local avec Mailtrap, ouvrez la sandbox « Email Testing » pour cliquer sur le lien de confirmation.</p>
    @endif
    <div class="d-flex flex-wrap gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-shae">Renvoyer le lien</button>
        </form>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Continuer sur le catalogue</a>
    </div>
</div>
@endsection
