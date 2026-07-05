<?php

namespace App\Mail;

use App\Models\ConcessionairePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRecordedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ConcessionairePayment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Received — ₱' . number_format((float) $this->payment->amount, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-recorded',
        );
    }
}
