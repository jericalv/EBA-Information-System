<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentDueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $concessionaire) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Due Reminder — ' . $this->dueDate(),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-due-reminder',
            with: [
                'name' => $this->concessionaire->business_name ?: $this->concessionaire->name,
                'monthlyFee' => '₱' . number_format((float) $this->concessionaire->monthly_fee, 2),
                'dueDate' => $this->dueDate(),
                'paymentsUrl' => route('concessionaire.payments'),
            ],
        );
    }

    private function dueDate(): string
    {
        return 'the 1st of ' . now()->format('F Y');
    }
}