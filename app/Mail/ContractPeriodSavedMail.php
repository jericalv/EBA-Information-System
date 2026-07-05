<?php

namespace App\Mail;

use App\Models\PartnershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractPeriodSavedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnershipApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contract Period Set for Your Concessionaire Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contract-period-saved',
        );
    }
}
