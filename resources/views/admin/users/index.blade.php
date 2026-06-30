@extends('layouts.admin')

@section('sidebar')
<a class="nav-link text-white" href="{{ route('admin.dashboard') }}">Dashboard</a>
<a class="nav-link text-white" href="{{ route('admin.users.index') }}">Utilisateurs</a>
@endsection

@section('content')
<h1>Gestion des utilisateurs</h1>
<form class="row g-2 mb-3">
    <div class="col-md-4"><input type="text" name="q" class="form-control" placeholder="Rechercher..." value="{{ request('q') }}"></div>
    <div class="col-md-3">
        <select name="role" class="form-select">
            <option value="">Tous les rôles</option>
            @foreach(['admin','gestionnaire','client'] as $role)
                <option value="{{ $role }}" @selected(request('role')===$role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary">Filtrer</button></div>
</form>
<table class="table table-striped">
    <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Actif</th><th>Actions</th></tr></thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->label() }}</td>
                <td>{{ $user->is_active ? 'Oui' : 'Non' }}</td>
                <td class="d-flex gap-2">
                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST">@csrf @method('PATCH')<button class="btn btn-sm btn-warning">Toggle</button></form>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Supprimer</button></form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $users->links() }}
@endsection
