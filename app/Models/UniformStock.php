<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UniformStock extends Model
{
    public const SIZE_KEYS = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];

    protected $fillable = [
        'item_name',
        'icon',
        'image',
        'item_type',
        'sizes',
        'prices',
        'unit_price',
        'quantity',
        'low_stock_threshold',
        'is_visible',
    ];

    protected $casts = [
        'sizes' => 'array',
        'prices' => 'array',
        'unit_price' => 'float',
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_visible' => 'boolean',
    ];

    public function salesOrderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isUniform(): bool
    {
        return $this->item_type === 'uniforms' && is_array($this->sizes) && $this->sizes !== [];
    }

    /**
     * Sizes this item actually carries, mapped to their current quantity.
     *
     * Legacy rows store all nine sizes with zeros for sizes never stocked, so a
     * size counts as carried when it has stock or a price. If nothing matches
     * (a fully zeroed legacy row), every stored size is treated as carried.
     *
     * @return array<string, int>
     */
    public function carriedSizes(): array
    {
        if (! $this->isUniform()) {
            return [];
        }

        $prices = is_array($this->prices) ? $this->prices : [];
        $carried = [];

        foreach (self::SIZE_KEYS as $size) {
            if (! array_key_exists($size, $this->sizes)) {
                continue;
            }

            $qty = (int) ($this->sizes[$size] ?? 0);
            $price = (float) ($prices[$size] ?? 0);

            if ($qty > 0 || $price > 0) {
                $carried[$size] = $qty;
            }
        }

        if ($carried === []) {
            foreach (self::SIZE_KEYS as $size) {
                if (array_key_exists($size, $this->sizes)) {
                    $carried[$size] = (int) ($this->sizes[$size] ?? 0);
                }
            }
        }

        return $carried;
    }

    /**
     * Stock health computed per carried size for uniforms, on the flat
     * quantity for everything else.
     *
     * @return array{status: string, low_sizes: array<int, string>, out_sizes: array<int, string>}
     */
    public function stockHealth(): array
    {
        $threshold = max(0, (int) ($this->low_stock_threshold ?? 10));

        if ($this->isUniform()) {
            $carried = $this->carriedSizes();
            $out = [];
            $low = [];

            foreach ($carried as $size => $qty) {
                if ($qty === 0) {
                    $out[] = $size;
                } elseif ($qty <= $threshold) {
                    $low[] = $size;
                }
            }

            if ($carried !== [] && count($out) === count($carried)) {
                $status = 'out';
            } elseif ($out !== [] || $low !== []) {
                $status = 'low';
            } else {
                $status = 'healthy';
            }

            return ['status' => $status, 'low_sizes' => $low, 'out_sizes' => $out];
        }

        $qty = (int) $this->quantity;
        $status = $qty === 0 ? 'out' : ($qty <= $threshold ? 'low' : 'healthy');

        return ['status' => $status, 'low_sizes' => [], 'out_sizes' => []];
    }

    /**
     * Selling price range across carried sizes, or the flat unit price.
     *
     * @return array{0: float, 1: float}|null [min, max] or null when unpriced
     */
    public function priceRange(): ?array
    {
        if ($this->isUniform()) {
            $priceMap = is_array($this->prices) ? $this->prices : [];
            $prices = [];

            foreach (array_keys($this->carriedSizes()) as $size) {
                $price = (float) ($priceMap[$size] ?? 0);
                if ($price > 0) {
                    $prices[] = $price;
                }
            }

            return $prices === [] ? null : [min($prices), max($prices)];
        }

        $unitPrice = (float) ($this->unit_price ?? 0);

        return $unitPrice > 0 ? [$unitPrice, $unitPrice] : null;
    }

    /**
     * Current stock valued at selling price (qty x price per carried size).
     */
    public function inventoryValue(): float
    {
        if ($this->isUniform()) {
            $priceMap = is_array($this->prices) ? $this->prices : [];
            $total = 0.0;

            foreach ($this->carriedSizes() as $size => $qty) {
                $total += $qty * (float) ($priceMap[$size] ?? 0);
            }

            return $total;
        }

        return (int) $this->quantity * (float) ($this->unit_price ?? 0);
    }
}
