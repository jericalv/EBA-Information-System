<?php

namespace App\Http\Controllers;

use App\Models\ConcessionairePayment;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Download a payment invoice via a signed URL (no login required).
     * The 'signed' middleware guarantees the link came from an email we sent.
     */
    public function download(ConcessionairePayment $payment)
    {
        $payment->loadMissing(['concessionaire', 'recordedBy', 'partnershipApplication']);

        $receiptYear = $payment->payment_date?->format('Y') ?? now()->format('Y');
        $receiptNumber = sprintf('RCP-%s-%05d', $receiptYear, $payment->id);
        $logoPath = public_path('images/eba-logo.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('pdf.payment-receipt', [
            'payment' => $payment,
            'receiptNumber' => $receiptNumber,
            'generatedAt' => now(),
            'logoDataUri' => $logoDataUri,
        ])->setPaper('a4');

        return $pdf->download(sprintf('invoice-%s.pdf', $receiptNumber));
    }
}
