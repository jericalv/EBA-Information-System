<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity.
     */
    public static function log(string $action, string $subjectType, ?string $subjectId, string $description, array $details = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'details' => $details ?: null,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Color for action badge.
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'approved', 'confirmed' => '#0A5C2F',
            'rejected' => '#dc2626',
            'cancelled' => '#d97706',
            'created' => '#2563eb',
            'updated' => '#7c3aed',
            default => '#64748b',
        };
    }
}
