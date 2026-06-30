@extends('layouts.app')

@section('title', 'Inscription — SHAE')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4 border-0 shadow-sm">
            <div class="text-center mb-3">
                <div class="h3 fw-bold text-shae">SHAE</div>
                <p class="text-muted small">Créez votre compte client</p>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Téléphone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mot de passe</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button class="btn btn-shae w-100 py-2">S'inscrire</button>
            </form>
        </div>
    </div>
</div>
@endsection
