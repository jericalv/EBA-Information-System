<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'uniform_stock_id',
        'user_id',
        'type',
        'size',
        'quantity_change',
        'quantity_after',
        'note',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_after' => 'integer',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(UniformStock::class, 'uniform_stock_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
