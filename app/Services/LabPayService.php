<?php

namespace App\Services;

use App\Models\Payment;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LabPayService
{
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '243') && strlen($digits) === 12) {
            $digits = '0'.substr($digits, 3);
        }

        return $digits;
    }

    public function isValidPhone(string $phone): bool
    {
        return (bool) preg_match('/^0\d{9}$/', $this->normalizePhone($phone));
    }

    public function initiatePayment(Payment $payment, ?string $callbackUrl = null): array
    {
        $config = config('shae.labpay');

        if (empty($config['api_key'])) {
            return [
                'status' => 'simulated',
                'success' => true,
                'transaction_id' => 'SIM-'.$payment->reference,
                'message' => 'Mode simulation — configurez LABPAY_API_KEY pour le paiement réel.',
            ];
        }

        $phone = $this->normalizePhone($payment->phone);
        $callbackUrl = $callbackUrl ?: PublicUrl::callbackUrl();

        Log::info('LabPay initiate', [
            'payment' => $payment->reference,
            'callback' => $callbackUrl,
        ]);

        try {
            $response = Http::acceptJson()
                ->timeout(30)
                ->post($this->mobileEndpoint(), [
                    'token' => $config['api_key'],
                    'phone' => $phone,
                    'amount' => (float) $payment->amount,
                    'currency' => $config['currency'],
                    'country' => $config['country'],
                    'reference' => $payment->reference,
                    'callback' => $callbackUrl,
                ]);

            $payload = $response->json() ?? [];

            if (! $response->successful() || ! ($payload['success'] ?? false)) {
                Log::warning('LabPay initiate rejected', [
                    'payment' => $payment->reference,
                    'http' => $response->status(),
                    'body' => $payload,
                ]);

                return [
                    'status' => 'error',
                    'success' => false,
                    'message' => $payload['message'] ?? 'Impossible d\'initier le paiement LabPay.',
                    'errors' => $payload['errors'] ?? null,
                    'http_status' => $response->status(),
                ];
            }

            return array_merge($payload, [
                'status' => data_get($payload, 'results.status.name', 'processed'),
                'success' => true,
                'transaction_id' => $payload['orderNumber'] ?? $payload['reference'] ?? null,
                'message' => $payload['message'] ?? 'Push USSD envoyé. Validez avec votre code PIN sur le téléphone.',
            ]);
        } catch (\Throwable $e) {
            Log::error('LabPay initiate error: '.$e->getMessage());

            return [
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function verifyPayment(Payment $payment, array $payload): bool
    {
        if (empty(config('shae.labpay.api_key'))) {
            return ($payload['status'] ?? '') === 'success';
        }

        $statusCode = data_get($payload, 'results.status.code');

        if ($statusCode !== null && (int) $statusCode === 2) {
            return true;
        }

        $statusName = strtolower((string) data_get($payload, 'results.status.name', ''));

        if (in_array($statusName, ['success', 'paid', 'completed', 'done'], true)) {
            return true;
        }

        if (($payload['success'] ?? false) === true) {
            return true;
        }

        return in_array(strtolower((string) ($payload['status'] ?? '')), ['success', 'completed', 'paid', 'done'], true);
    }

    public function isFailedCallback(array $payload): bool
    {
        $statusCode = data_get($payload, 'results.status.code');

        return $statusCode !== null && (int) $statusCode === 3;
    }

    private function mobileEndpoint(): string
    {
        $base = rtrim(config('shae.labpay.api_url'), '/');

        if (str_ends_with($base, '/api/V1/payment')) {
            return $base.'/mobile';
        }

        return $base.'/api/V1/payment/mobile';
    }
}
