<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PaymentsRecordedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, \App\Models\ConcessionairePayment>  $payments
     */
    public function __construct(
        public User $concessionaire,
        public Collection $payments,
        public Carbon $receivedDate
    ) {}

    public function envelope(): Envelope
    {
        $total = $this->payments->sum(fn ($payment) => (float) $payment->amount);

        return new Envelope(
            subject: 'Payment Received — ₱' . number_format($total, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payments-recorded',
        );
    }
}
