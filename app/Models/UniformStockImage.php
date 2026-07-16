<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniformStockImage extends Model
{
    protected $fillable = [
        'uniform_stock_id',
        'path',
        'sort_order',
    ];

    /**
     * Get the stock item that owns the image.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(UniformStock::class, 'uniform_stock_id');
    }
}
