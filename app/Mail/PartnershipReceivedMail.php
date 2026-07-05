<?php

namespace App\Mail;

use App\Models\PartnershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnershipReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnershipApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Partnership Application Received',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.partnership-received',
        );
    }
}
