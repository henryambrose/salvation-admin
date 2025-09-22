<?php

namespace Modules\Fund\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityContributionType extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get all community contributions for this type
     */
    public function communityContributions()
    {
        return $this->hasMany(CommunityContribution::class, 'contribution_type_id');
    }

    /**
     * Get active community contributions for this type
     */
    public function activeCommunityContributions()
    {
        return $this->hasMany(CommunityContribution::class, 'contribution_type_id')
                    ->where('status', '!=', 'archived');
    }

    /**
     * Scope to get only active contribution types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order and name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the creator user
     */
    public function creator()
    {
        return $this->belongsTo(\Modules\Members\Models\User::class, 'created_by');
    }

    /**
     * Get the updater user
     */
    public function updater()
    {
        return $this->belongsTo(\Modules\Members\Models\User::class, 'updated_by');
    }

    /**
     * Get total amount collected for this contribution type
     */
    public function getTotalAmountAttribute()
    {
        return $this->communityContributions()
                   ->where('status', '!=', 'archived')
                   ->sum('amount');
    }

    /**
     * Get count of contributions for this type
     */
    public function getContributionsCountAttribute()
    {
        return $this->communityContributions()
                   ->where('status', '!=', 'archived')
                   ->count();
    }
}