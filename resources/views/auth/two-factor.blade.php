@extends('layouts.app')

@section('title', 'Vérification 2FA — SHAE')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card p-4">
            <h2 class="h4 mb-3">Vérification en deux étapes</h2>
            <p class="text-muted">Entrez le code reçu par email (valide {{ config('shae.otp_expiration_minutes') }} minutes).</p>
            @if(config('mail.default') === 'log' && ($devOtpCode ?? null))
                <div class="alert alert-light border small mb-3">
                    En local, le code est affiché ici pour les tests :
                    <strong class="text-shae">{{ $devOtpCode }}</strong>
                </div>
            @endif
            <form method="POST" action="{{ route('two-factor.verify') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Code OTP</label>
                    <input type="text" name="code" class="form-control" maxlength="6" required>
                </div>
                <button class="btn btn-shae w-100">Valider</button>
            </form>
        </div>
    </div>
</div>
@endsection
