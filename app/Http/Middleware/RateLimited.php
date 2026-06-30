<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimited
{
    public function handle(Request $request, Closure $next, string $key = 'global', int $maxAttempts = 60): Response
    {
        $rateKey = $key.':'.$request->ip();

        if (RateLimiter::tooManyAttempts($rateKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateKey);

            return response('Trop de requêtes. Réessayez dans '.$seconds.' secondes.', 429);
        }

        RateLimiter::hit($rateKey, 60);

        return $next($request);
    }
}
