@extends('layouts.app')

@section('content')
<h1>Mon profil</h1>
<form method="POST" action="{{ route('profile.update') }}" class="card p-4">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
    </div>
    <div class="form-check mb-3">
        <input type="checkbox" name="two_factor_enabled" value="1" class="form-check-input" id="2fa" @checked(old('two_factor_enabled', $user->two_factor_enabled))>
        <label class="form-check-label" for="2fa">Activer la double authentification (2FA)</label>
    </div>
    <button class="btn btn-shae">Enregistrer</button>
</form>
@endsection
