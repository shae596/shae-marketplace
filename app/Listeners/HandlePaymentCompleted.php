<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Mail\PaymentReceiptMail;
use App\Mail\StatusNotificationMail;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class HandlePaymentCompleted
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment->load(['order.items.product', 'user']);

        foreach ($payment->order->items as $item) {
            Product::where('id', $item->product_id)
                ->where('stock', '<', 999)
                ->decrement('stock', $item->quantity);
        }

        try {
            Mail::to($payment->user->email)->send(new PaymentReceiptMail($payment->user, $payment));
        } catch (Throwable $e) {
            Log::warning('Reçu paiement non envoyé.', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to($payment->user->email)->send(
                new StatusNotificationMail($payment->user, 'Votre paiement a été confirmé avec succès.')
            );
        } catch (Throwable $e) {
            Log::warning('Notification paiement non envoyée.', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
        }
    }
}
