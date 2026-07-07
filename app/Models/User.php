<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\ConcessionairePayment;
use App\Models\PartnershipApplication;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'business_name',
        'email',
        'phone_number',
        'password',
        'role',
        'monthly_fee',
        'is_active_concessionaire',
        'is_approved',
        'location',
        'description',
        'cover_photo',
        'profile_photo',
        'carousel_image',
        'notification_preferences',
        'payment_notifications_read_at',
        'application_notifications_read_at',
    ];

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a concessionaire.
     */
    public function isConcessionaire(): bool
    {
        return $this->role === 'concessionaire';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'monthly_fee' => 'decimal:2',
            'is_active_concessionaire' => 'boolean',
            'is_approved' => 'boolean',
            'notification_preferences' => 'array',
            'payment_notifications_read_at' => 'datetime',
            'application_notifications_read_at' => 'datetime',
        ];
    }

    /**
     * Resolve a notification preference key with a default of true.
     */
    public function getNotificationPreference(string $key): bool
    {
        $preferences = is_array($this->notification_preferences)
            ? $this->notification_preferences
            : [];

        return (bool) ($preferences[$key] ?? true);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Products owned by this concessionaire.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'concessionaire_id');
    }

    /**
     * Carousel banner images uploaded by this concessionaire.
     */
    public function carouselMedia(): HasMany
    {
        return $this->hasMany(ConcessionaireMedia::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Store-level reviews received by this concessionaire.
     */
    public function concessionaireReviews(): HasMany
    {
        return $this->hasMany(ConcessionaireReview::class, 'concessionaire_id');
    }

    /**
     * Store-level reviews authored by this user.
     */
    public function givenConcessionaireReviews(): HasMany
    {
        return $this->hasMany(ConcessionaireReview::class, 'user_id');
    }

    /**
     * Payments received by this concessionaire.
     */
    public function concessionairePayments(): HasMany
    {
        return $this->hasMany(ConcessionairePayment::class, 'concessionaire_id');
    }

    /**
     * Payments recorded by this user as cashier.
     */
    public function recordedPayments(): HasMany
    {
        return $this->hasMany(ConcessionairePayment::class, 'recorded_by');
    }

    /**
     * Sales orders recorded by this cashier.
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'cashier_id');
    }

    /**
     * Latest partnership application linked to this user.
     */
    public function latestPartnershipApplication(): HasOne
    {
        return $this->hasOne(PartnershipApplication::class, 'user_id')->latestOfMany();
    }

    /**
     * Average store rating for this concessionaire.
     */
    public function getConcessionaireAverageRatingAttribute(): float
    {
        return round($this->concessionaireReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Total number of store reviews for this concessionaire.
     */
    public function getConcessionaireReviewCountAttribute(): int
    {
        return $this->concessionaireReviews()->count();
    }
}
