<?php

namespace App\Http\Middleware;

use App\Support\PublicUrl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceRequestRootUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $rootUrl = PublicUrl::rootUrl($request);

        if ($rootUrl !== '') {
            URL::forceRootUrl($rootUrl);
            URL::forceScheme(parse_url($rootUrl, PHP_URL_SCHEME) ?: 'https');
        }

        return $next($request);
    }
}
