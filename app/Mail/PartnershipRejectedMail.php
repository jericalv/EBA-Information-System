<?php

namespace App\Mail;

use App\Models\PartnershipApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnershipRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User|PartnershipApplication $concessionaire,
        public ?string $rejectionReason = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'EBA Partnership Application — Update Required',
        );
    }

    public function content(): Content
    {
        $name = $this->concessionaire instanceof PartnershipApplication
            ? ($this->concessionaire->business_name ?: $this->concessionaire->full_name)
            : ($this->concessionaire->business_name ?: $this->concessionaire->name);

        $reason = $this->rejectionReason
            ?? ($this->concessionaire instanceof PartnershipApplication
                ? $this->concessionaire->rejection_reason
                : null)
            ?? 'Not specified.';

        return new Content(
            markdown: 'emails.partnership-rejected',
            with: [
                'name' => $name,
                'rejectionReason' => $reason,
                'resubmitUrl' => route('application'),
            ],
        );
    }
}
