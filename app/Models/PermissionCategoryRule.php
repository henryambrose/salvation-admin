<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermissionCategoryRule extends Model
{
    protected $fillable = [
        'permission_category_id', 'rule_type', 'rule_value', 'priority', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the category that owns this rule.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PermissionCategory::class, 'permission_category_id');
    }

    /**
     * Scope for active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}


