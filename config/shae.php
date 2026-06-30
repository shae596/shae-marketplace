<?php

return [
    'labpay' => [
        'api_url' => env('LABPAY_API_URL', 'https://api.labyrinthe-rdc.com'),
        'api_key' => (static function (): ?string {
            $file = storage_path('app/labpay-api-token.txt');
            if (is_readable($file)) {
                $token = trim((string) file_get_contents($file));
                if ($token !== '') {
                    return $token;
                }
            }

            $fromEnv = env('LABPAY_API_KEY');

            return $fromEnv !== '' && $fromEnv !== null ? $fromEnv : null;
        })(),
        'callback_url' => ($url = env('LABPAY_CALLBACK_URL')) ? trim((string) $url) : null,
        'currency' => env('LABPAY_CURRENCY', 'USD'),
        'country' => env('LABPAY_COUNTRY', 'CD'),
    ],
    'otp_expiration_minutes' => env('OTP_EXPIRATION_MINUTES', 10),
    'upload' => [
        'max_kilobytes' => (int) env('UPLOAD_MAX_KB', 10240),
    ],
];
