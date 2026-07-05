<?php

namespace App\Mail;

use App\Models\PartnershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnershipStepApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PartnershipApplication $application,
        public string $stepLabel,
        public string $heading,
        public string $body,
        public string $nextStep
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->stepLabel . ' Approved — EBA Partnership',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.partnership-step-approved',
        );
    }
}
