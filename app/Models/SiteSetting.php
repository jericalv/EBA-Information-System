<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key, with an optional default fallback.
     */
    public static function get(string $key, string $default = ''): string
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    /**
     * Resolve an image setting to a usable URL.
     *
     * Uploaded images are stored as a relative path on the "public" disk
     * (e.g. "site/abc.jpg"). When no custom image has been uploaded yet,
     * the provided default URL is returned instead.
     */
    public static function image(string $key, string $default = ''): string
    {
        $value = trim(static::get($key, ''));

        if ($value === '') {
            return $default;
        }

        // Already a full URL or absolute path — use as-is.
        if (Str::startsWith($value, ['http://', 'https://', '/'])) {
            return $value;
        }

        return asset('storage/' . ltrim($value, '/'));
    }
}
