<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcessionaireMedia extends Model
{
    protected $table = 'concessionaire_media';

    protected $fillable = [
        'user_id',
        'path',
        'sort_order',
    ];

    /**
     * The concessionaire that owns this media item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Public URL for the stored image.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
