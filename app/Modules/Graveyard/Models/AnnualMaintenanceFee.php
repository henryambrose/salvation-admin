<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Members\Models\User;

class AnnualMaintenanceFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'year',
        'permanent_grave_amount',
        'niche_amount',
        'effective_from',
        'effective_until',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'formatted_permanent_grave_amount',
        'formatted_niche_amount',
    ];

    protected $casts = [
        'effective_from' => \App\Casts\DateString::class,
        'effective_until' => \App\Casts\DateString::class,
        'is_active' => 'boolean',
        'permanent_grave_amount' => 'decimal:2',
        'niche_amount' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * Get the user who created this record
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get active fees only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get fees for a specific year
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Get formatted permanent grave amount
     */
    public function getFormattedPermanentGraveAmountAttribute(): string
    {
        return $this->permanent_grave_amount ? '₹ ' . number_format($this->permanent_grave_amount, 2) : 'N/A';
    }

    /**
     * Get formatted niche amount
     */
    public function getFormattedNicheAmountAttribute(): string
    {
        return $this->niche_amount ? '₹ ' . number_format($this->niche_amount, 2) : 'N/A';
    }

    /**
     * Static method to get maintenance fee for a specific year and type
     */
    public static function getFeeForYear(int $year, string $type = 'permanent_grave'): ?float
    {
        static $cache = [];
        $key = "{$type}_{$year}";

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        // Try exact year first
        $fee = static::active()->forYear($year)->first();
        if ($fee) {
            return $cache[$key] = $type === 'niche' ? $fee->niche_amount : $fee->permanent_grave_amount;
        }

        // Fall back to most recent fee before or in the requested year that has the requested amount type
        $latestFee = static::active()
            ->where('year', '<=', $year)
            ->whereNotNull($type === 'niche' ? 'niche_amount' : 'permanent_grave_amount')
            ->orderBy('year', 'desc')
            ->first();

        return $cache[$key] = $latestFee ? ($type === 'niche' ? $latestFee->niche_amount : $latestFee->permanent_grave_amount) : null;
    }

    /**
     * Static method to get maintenance fee for a specific year and type (alias for backward compatibility)
     */
    public static function getFeeForYearAndType(int $year, string $type = 'permanent_grave'): ?float
    {
        return static::getFeeForYear($year, $type);
    }

    /**
     * Static method to get the current maintenance fee
     */
    public static function getCurrentFee(string $type = 'permanent_grave'): ?float
    {
        return static::getFeeForYear(now()->year, $type);
    }

    /**
     * Check if this fee can be edited
     */
    public function canBeEdited(): bool
    {
        return true; // Simplified - always allow editing
    }

    /**
     * Check if this fee can be deleted
     */
    public function canBeDeleted(): bool
    {
        return true; // Simplified - always allow deletion
    }

    /**
     * Check if a fee exists for the current year
     */
    public static function hasCurrentYearFee(): bool
    {
        return static::active()->forYear(now()->year)->exists();
    }

    /**
     * Get the latest fee year available
     */
    public static function getLatestFeeYear(): ?int
    {
        return static::active()->max('year');
    }

    /**
     * Check if we're using fallback fee for a given year
     */
    public static function isUsingFallbackFee(int $year): bool
    {
        return !static::active()->forYear($year)->exists() && static::getLatestFeeYear() < $year;
    }

    /**
     * Get fee information with fallback details
     */
    public static function getFeeWithFallbackInfo(int $year, string $type = 'permanent_grave'): array
    {
        // Try exact year first
        $exactFee = static::active()->forYear($year)->first();
        if ($exactFee) {
            return [
                'amount' => $type === 'niche' ? $exactFee->niche_amount : $exactFee->permanent_grave_amount,
                'year_used' => $year,
                'is_fallback' => false,
                'message' => null
            ];
        }

        // Fall back to most recent fee that has the requested amount type
        $latestFee = static::active()
            ->where('year', '<=', $year)
            ->whereNotNull($type === 'niche' ? 'niche_amount' : 'permanent_grave_amount')
            ->orderBy('year', 'desc')
            ->first();

        if ($latestFee) {
            return [
                'amount' => $type === 'niche' ? $latestFee->niche_amount : $latestFee->permanent_grave_amount,
                'year_used' => $latestFee->year,
                'is_fallback' => true,
                'message' => "No maintenance fee set for {$year}. Using {$latestFee->year} rates."
            ];
        }

        return [
            'amount' => null,
            'year_used' => null,
            'is_fallback' => false,
            'message' => "No maintenance fees found."
        ];
    }
}