<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcessionairePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'partnership_application_id',
        'concessionaire_id',
        'recorded_by',
        'amount',
        'payment_date',
        'period_month',
        'payment_type',
        'or_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'period_month' => 'date',
    ];

    /**
     * Advance / On Time / Late, comparing the month the payment was made
     * against the month it covers. Null when either date is missing.
     */
    public function paymentTiming(): ?string
    {
        if (! $this->payment_date || ! $this->period_month) {
            return null;
        }

        $paid = $this->payment_date->format('Y-m');
        $period = $this->period_month->format('Y-m');

        if ($paid < $period) {
            return 'Advance';
        }

        if ($paid > $period) {
            return 'Late';
        }

        return 'On Time';
    }

    public function concessionaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concessionaire_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function partnershipApplication(): BelongsTo
    {
        return $this->belongsTo(PartnershipApplication::class);
    }
}
