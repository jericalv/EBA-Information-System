<?php

namespace App\Mail;

use App\Models\PartnershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnershipDocumentUploadedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnershipApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Partnership Document Uploaded',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.partnership-document-uploaded',
        );
    }
}
