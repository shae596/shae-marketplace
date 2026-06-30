<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

trait RedirectsAfterAuthentication
{
    protected function redirectAfterAuthentication(User $user): RedirectResponse
    {
        $default = route($user->dashboardRoute());

        if ($user->hasRole(UserRole::Client)) {
            session()->forget('url.intended');

            return redirect()->to($default);
        }

        return redirect()->intended($default);
    }
}
