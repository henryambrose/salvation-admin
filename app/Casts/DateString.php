<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * DateString Cast
 *
 * This cast handles date fields as simple strings (YYYY-MM-DD format)
 * without any timezone conversion. This prevents the common issue where
 * dates shift by one day due to timezone offsets during serialization.
 *
 * Use this instead of Laravel's built-in 'date' cast to avoid timezone issues.
 */
class DateString implements CastsAttributes
{
    /**
     * Cast the given value.
     * Returns the date as a simple string without timezone conversion.
     *
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // Return date as-is (string format YYYY-MM-DD)
        // No Carbon conversion, no timezone issues
        return $value;
    }

    /**
     * Prepare the given value for storage.
     * Accepts various date formats and normalizes to YYYY-MM-DD.
     *
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        // If already in YYYY-MM-DD format, return as-is
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Handle DD/MM/YYYY format (from frontend)
        if (is_string($value) && preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            return "{$year}-{$month}-{$day}";
        }

        // Handle Carbon/DateTime objects (fallback)
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        // Try to parse with strtotime as last resort
        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        // Invalid date, return null
        return null;
    }
}
