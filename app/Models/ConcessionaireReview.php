<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcessionaireReview extends Model
{
    protected $fillable = [
        'user_id',
        'concessionaire_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function concessionaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concessionaire_id');
    }
}
