<?php

namespace App\Mail;

use App\Models\ConcessionairePayment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

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
            with: [
                'invoiceUrls' => $this->payments->mapWithKeys(fn (ConcessionairePayment $payment) => [
                    $payment->id => URL::signedRoute('invoice.download', ['payment' => $payment->id]),
                ]),
            ],
        );
    }

    /**
     * Attach the official invoice PDF for each recorded payment,
     * using the same template as the in-portal receipt download.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $logoPath = public_path('images/eba-logo.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        return $this->payments->map(function (ConcessionairePayment $payment) use ($logoDataUri) {
            $payment->loadMissing(['concessionaire', 'recordedBy', 'partnershipApplication']);

            $receiptYear = $payment->payment_date?->format('Y') ?? now()->format('Y');
            $receiptNumber = sprintf('RCP-%s-%05d', $receiptYear, $payment->id);

            $pdf = Pdf::loadView('pdf.payment-receipt', [
                'payment' => $payment,
                'receiptNumber' => $receiptNumber,
                'generatedAt' => now(),
                'logoDataUri' => $logoDataUri,
            ])->setPaper('a4');

            return Attachment::fromData(fn () => $pdf->output(), sprintf('invoice-%s.pdf', $receiptNumber))
                ->withMime('application/pdf');
        })->all();
    }
}
