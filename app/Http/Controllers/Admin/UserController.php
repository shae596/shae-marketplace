<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->q, fn ($q) => $q->where('name', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Statut du compte mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé.');
    }
}
