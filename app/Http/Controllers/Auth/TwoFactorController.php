<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\RedirectsAfterAuthentication;
use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    use RedirectsAfterAuthentication;

    public function __construct(private OtpService $otpService)
    {
    }

    public function show()
    {
        if (! session('otp_user_id')) {
            return redirect()->route('login');
        }

        $devOtpCode = null;

        if (app()->isLocal() && config('mail.default') === 'log') {
            $devOtpCode = OtpCode::where('user_id', session('otp_user_id'))
                ->where('used', false)
                ->latest()
                ->first()
                ?->code;
        }

        return view('auth.two-factor', compact('devOtpCode'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = session('otp_user_id');

        if (! $userId || ! $this->otpService->verify($userId, $request->code)) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        session()->forget('otp_user_id');
        Auth::loginUsingId($userId);
        $request->session()->regenerate();

        return $this->redirectAfterAuthentication(Auth::user())
            ->with('success', 'Connexion réussie.');
    }
}
