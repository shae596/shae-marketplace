<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\RedirectsAfterAuthentication;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use RedirectsAfterAuthentication;

    public function __construct(private OtpService $otpService)
    {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors(['email' => 'Votre compte est désactivé.']);
        }

        if ($user->two_factor_enabled) {
            Auth::logout();
            $this->otpService->generateAndSend($user);
            session(['otp_user_id' => $user->id]);

            return redirect()->route('two-factor.show');
        }

        $request->session()->regenerate();

        return $this->redirectAfterAuthentication($user)
            ->with('success', 'Bienvenue sur SHAE !');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'role' => UserRole::Client,
            'is_active' => true,
            'email_verified_at' => app()->isLocal() ? now() : null,
        ]);

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->hasVerifiedEmail()) {
            return $this->redirectAfterAuthentication($user)
                ->with('success', 'Votre compte client a été créé avec succès !');
        }

        return redirect()->route('verification.notice')
            ->with('success', 'Compte créé ! Consultez votre boîte email pour confirmer votre adresse.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
