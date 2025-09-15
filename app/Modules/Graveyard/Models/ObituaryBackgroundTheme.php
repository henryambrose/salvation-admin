<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ObituaryBackgroundTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'type',
        'tier',
        'image_path',
        'style_properties',
        'background_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'style_properties' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only active themes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get themes by tier
     */
    public function scopeByTier(Builder $query, string $tier): Builder
    {
        return $query->where('tier', $tier);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get themes available for a specific tier level
     */
    public static function getAvailableForTier(string $userTier = 'basic'): \Illuminate\Database\Eloquent\Collection
    {
        $allowedTiers = $userTier === 'premium' ? ['basic', 'premium'] : ['basic'];

        return self::active()
            ->whereIn('tier', $allowedTiers)
            ->ordered()
            ->get();
    }

    /**
     * Get theme by key
     */
    public static function getByKey(string $key): ?self
    {
        return self::where('key', $key)->active()->first();
    }

    /**
     * Get the complete style array for CSS
     */
    public function getCompleteStyleAttribute(): array
    {
        $style = $this->style_properties ?? [];

        // Add image path if available
        if ($this->image_path && $this->type === 'image') {
            $style['backgroundImage'] = "url('{$this->image_path}')";
            $style['backgroundSize'] = $style['backgroundSize'] ?? 'cover';
            $style['backgroundPosition'] = $style['backgroundPosition'] ?? 'center';
            $style['backgroundRepeat'] = $style['backgroundRepeat'] ?? 'no-repeat';
        }

        // Add background color as fallback
        if ($this->background_color) {
            $style['backgroundColor'] = $this->background_color;
        }

        return $style;
    }

    /**
     * Get the theme formatted for frontend use
     */
    public function toFrontendArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'tier' => $this->tier,
            'image' => $this->image_path,
            'style' => $this->getCompleteStyleAttribute(),
        ];
    }
}
