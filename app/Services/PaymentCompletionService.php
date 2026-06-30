<?php

namespace App\Services;

use App\Events\PaymentCompleted;
use App\Models\Payment;

class PaymentCompletionService
{
    public function __construct(private LabPayService $labPayService)
    {
    }

    public function markAsSuccessful(Payment $payment, array $providerPayload = []): bool
    {
        if ($payment->status === 'success') {
            return true;
        }

        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
            'provider_response' => array_merge($payment->provider_response ?? [], $providerPayload),
        ]);

        $payment->order->update(['status' => 'paid']);

        PaymentCompleted::dispatch($payment->fresh(['order', 'user']));

        return true;
    }

    public function markAsFailed(Payment $payment, array $providerPayload = []): void
    {
        $payment->update([
            'status' => 'failed',
            'provider_response' => array_merge($payment->provider_response ?? [], $providerPayload),
        ]);
    }

    public function processCallback(Payment $payment, array $payload): bool
    {
        if ($this->labPayService->verifyPayment($payment, $payload)) {
            return $this->markAsSuccessful($payment, $payload);
        }

        $this->markAsFailed($payment, $payload);

        return false;
    }
}
