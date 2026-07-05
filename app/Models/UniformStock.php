<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformStock extends Model
{
    protected $fillable = [
        'item_name',
        'icon',
        'image',
        'item_type',
        'sizes',
        'prices',
        'unit_price',
        'quantity',
        'is_visible',
    ];

    protected $casts = [
        'sizes' => 'array',
        'prices' => 'array',
        'unit_price' => 'float',
        'quantity' => 'integer',
        'is_visible' => 'boolean',
    ];

    public function salesOrderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }
}
