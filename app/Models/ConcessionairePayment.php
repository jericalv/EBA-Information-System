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
        'payment_type',
        'or_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

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
