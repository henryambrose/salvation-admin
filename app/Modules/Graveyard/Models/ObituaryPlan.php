<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObituaryPlan extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'duration_in_days',
        'cost',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'duration_in_days' => 'integer',
    ];

    // Relationships
    public function obituaryPages(): HasMany
    {
        return $this->hasMany(ObituaryPage::class);
    }

    public function obituaryPayments(): HasMany
    {
        return $this->hasMany(ObituaryPayment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getFormattedCostAttribute(): string
    {
        return '₹' . number_format($this->cost, 2);
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_in_days) {
            return 'Lifetime';
        }

        if ($this->duration_in_days <= 31) {
            return $this->duration_in_days . ' days';
        }

        if ($this->duration_in_days <= 365) {
            $months = round($this->duration_in_days / 30);
            return $months . ' month' . ($months > 1 ? 's' : '');
        }

        $years = round($this->duration_in_days / 365);
        return $years . ' year' . ($years > 1 ? 's' : '');
    }

    public function isLifetime(): bool
    {
        return !$this->duration_in_days;
    }
}
