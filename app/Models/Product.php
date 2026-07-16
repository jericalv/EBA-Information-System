<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    protected $fillable = [
        'concessionaire_id',
        'name',
        'description',
        'price',
        'category',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the concessionaire that owns the product.
     */
    public function concessionaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concessionaire_id');
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Gallery images, in display order.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Gallery paths for display, falling back to the legacy single image column.
     *
     * @return array<int, string>
     */
    public function galleryPaths(): array
    {
        $paths = $this->images->pluck('path')->all();

        if (empty($paths) && $this->image) {
            $paths = [$this->image];
        }

        return array_slice($paths, 0, 5);
    }

    /**
     * Get the average rating for the product.
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get the total number of reviews.
     */
    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Scope to filter available products.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to exclude soft-deleted rows when deleted_at exists.
     */
    public function scopeNotDeleted($query)
    {
        if (Schema::hasColumn($this->getTable(), 'deleted_at')) {
            $query->whereNull($this->qualifyColumn('deleted_at'));
        }

        return $query;
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
