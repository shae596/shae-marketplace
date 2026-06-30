<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Support\PublicUrl;
use App\Services\LabPayService;
use App\Services\PaymentCompletionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        private LabPayService $labPayService,
        private PaymentCompletionService $paymentCompletionService
    ) {
    }

    public function initiate(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_if($order->status !== 'pending', 400);

        return view('payments.initiate', compact('order'));
    }

    public function process(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = $this->labPayService->normalizePhone($request->phone);

        if (! $this->labPayService->isValidPhone($phone)) {
            return back()
                ->withErrors(['phone' => 'Numéro invalide. Utilisez 10 chiffres RDC (ex: 0891234567).'])
                ->withInput();
        }

        $reference = 'PAY-'.strtoupper(Str::random(10));

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'amount' => $order->total,
            'provider' => 'labpay',
            'reference' => $reference,
            'phone' => $phone,
            'status' => 'pending',
        ]);

        $callbackUrl = PublicUrl::callbackUrl($request);

        $response = $this->labPayService->initiatePayment($payment, $callbackUrl);

        $payment->update([
            'external_id' => $response['transaction_id'] ?? null,
            'provider_response' => array_merge($response, [
                'callback_url_used' => $callbackUrl,
            ]),
        ]);

        if (($response['success'] ?? false) !== true && ($response['status'] ?? '') !== 'simulated') {
            $this->paymentCompletionService->markAsFailed($payment, $response);

            return back()
                ->withErrors(['phone' => $response['message'] ?? 'Échec de l\'initiation LabPay.'])
                ->withInput();
        }

        return redirect()->route('payments.status', $payment)
            ->with('success', $response['message'] ?? 'Paiement initié. Validez le push USSD avec votre code PIN.');
    }

    public function status(Payment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        return view('payments.status', compact('payment'));
    }

    public function callback(Request $request)
    {
        Log::info('LabPay callback received', $request->all());

        $payload = $request->all();

        $reference = $request->input('reference')
            ?? $request->input('orderNumber')
            ?? $request->input('transaction_reference')
            ?? data_get($payload, 'results.details.reference');

        $payment = null;

        if ($reference) {
            $payment = Payment::query()
                ->where('reference', $reference)
                ->orWhere('external_id', $reference)
                ->latest()
                ->first();
        }

        if (! $payment && $labyrintheRef = data_get($payload, 'reference')) {
            $payment = Payment::query()
                ->where('external_id', $labyrintheRef)
                ->orWhere('reference', $labyrintheRef)
                ->latest()
                ->first();
        }

        if (! $payment) {
            Log::warning('LabPay callback: payment not found', $payload);

            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        if ($this->labPayService->isFailedCallback($payload)) {
            $this->paymentCompletionService->markAsFailed($payment, $payload);

            return response()->json(['success' => true, 'status' => 'failed']);
        }

        $verified = $this->paymentCompletionService->processCallback($payment, $payload);

        return response()->json(['success' => $verified, 'received' => true]);
    }

    public function confirmPaid(Payment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);
        abort_unless($payment->status === 'pending', 400);
        abort_if(empty(config('shae.labpay.api_key')), 404);

        $this->paymentCompletionService->markAsSuccessful($payment, [
            'mode' => 'manual_confirm',
            'message' => 'Confirmé par le client après débit mobile (callback LabPay non reçu).',
        ]);

        return redirect()->route('payments.status', $payment)
            ->with('success', 'Paiement confirmé. Consultez votre email et le reçu PDF.');
    }

    public function simulate(Payment $payment)
    {
        abort_unless(app()->environment('local') || empty(config('shae.labpay.api_key')), 404);
        abort_unless($payment->user_id === auth()->id(), 403);
        abort_unless($payment->status === 'pending', 400);

        $this->paymentCompletionService->markAsSuccessful($payment, [
            'status' => 'success',
            'mode' => 'simulation',
        ]);

        return redirect()->route('payments.status', $payment)
            ->with('success', 'Paiement simulé avec succès.');
    }

    public function history()
    {
        $payments = Payment::with('order')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('payments.history', compact('payments'));
    }

    public function receipt(Payment $payment)
    {
        abort_unless($payment->user_id === auth()->id() || auth()->user()->hasRole('admin', 'gestionnaire'), 403);
        abort_unless($payment->status === 'success', 404);

        $pdf = Pdf::loadView('payments.receipt-pdf', compact('payment'));

        return $pdf->download('recu-'.$payment->reference.'.pdf');
    }
}
