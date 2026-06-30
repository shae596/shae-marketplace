<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'two_factor_enabled' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? $user->phone,
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
        ]);

        return back()->with('success', 'Profil mis à jour.');
    }
}
