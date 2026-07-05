<?php

namespace App\Mail;

use App\Models\PartnershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpiryWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PartnershipApplication $application,
        public int $days
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Concessionaire Contract Expires in {$this->days} Days",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contract-expiry-warning',
        );
    }
}
