<?php

namespace App\Support;

use Illuminate\Http\Request;

class PublicUrl
{
    public static function scheme(Request $request): string
    {
        if (filter_var(env('APP_FORCE_HTTPS', false), FILTER_VALIDATE_BOOL)) {
            return 'https';
        }

        $forwarded = strtolower((string) $request->header('X-Forwarded-Proto', ''));
        if ($forwarded === 'https') {
            return 'https';
        }

        if (self::hostRequiresHttps($request->getHost())) {
            return 'https';
        }

        return $request->getScheme();
    }

    public static function rootUrl(?Request $request = null): string
    {
        $request ??= request();

        $configured = rtrim((string) config('app.url'), '/');
        if (self::isHttpsPublicUrl($configured)) {
            return $configured;
        }

        $host = $request->getHost();
        if (in_array($host, ['localhost', '127.0.0.1'], true)) {
            return $configured !== '' ? $configured : $request->getSchemeAndHttpHost();
        }

        $scheme = self::scheme($request);
        $port = $request->getPort();
        $defaultPort = $scheme === 'https' ? 443 : 80;
        $portSuffix = ($port && (int) $port !== $defaultPort) ? ':'.$port : '';

        return $scheme.'://'.$host.$portSuffix;
    }

    public static function callbackUrl(?Request $request = null): string
    {
        $configured = config('shae.labpay.callback_url');
        if (is_string($configured) && trim($configured) !== '' && self::isHttpsPublicUrl(trim($configured))) {
            return rtrim(trim($configured), '/');
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if (self::isHttpsPublicUrl($appUrl)) {
            return $appUrl.'/payments/callback';
        }

        $request ??= request();

        return rtrim(self::rootUrl($request), '/').'/payments/callback';
    }

    public static function isHttpsPublicUrl(string $url): bool
    {
        if (! str_starts_with($url, 'https://')) {
            return false;
        }

        return ! str_contains($url, '127.0.0.1')
            && ! str_contains($url, 'localhost');
    }

    private static function hostRequiresHttps(string $host): bool
    {
        $host = strtolower($host);

        foreach ([
            '.trycloudflare.com',
            '.onrender.com',
            '.infinityfreeapp.com',
            '.rf.gd',
            '.42web.io',
            '.000webhostapp.com',
            '.epizy.com',
            '.byetcluster.com',
        ] as $suffix) {
            if (str_ends_with($host, $suffix)) {
                return true;
            }
        }

        return false;
    }
}
