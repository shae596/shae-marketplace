@extends('layouts.app')

@section('title', 'Connexion — SHAE')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card p-4 border-0 shadow-sm">
            <div class="text-center mb-4">
                <div class="display-6 fw-black text-shae" style="font-weight:900">SHAE</div>
                <p class="text-muted small mb-0">Connectez-vous à votre compte</p>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>
                <button class="btn btn-shae w-100 py-2">Se connecter</button>
            </form>
            <p class="text-center text-muted small mb-0 mt-3">
                Nouveau client ? <a href="{{ route('register') }}" class="text-shae fw-semibold">Créer un compte</a>
            </p>
        </div>
    </div>
</div>
@endsection
