<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public Payment $payment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reçu de paiement SHAE');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-receipt');
    }
}
