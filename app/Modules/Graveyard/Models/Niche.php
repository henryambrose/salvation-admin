<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Members\Models\Member;

class Niche extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'niche_no',
        'sr_no',
        'location',
        'status',
        'last_occupation_date',
        'owner_name',
        'member_id',
        'contact_no',
        'remarks',
        'size_width',
        'size_height',
        'size_depth',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_occupation_date' => 'date',
        'size_width' => 'decimal:2',
        'size_height' => 'decimal:2',
        'size_depth' => 'decimal:2',
        'is_active' => 'boolean',
        'niche_no' => 'integer',
        'sr_no' => 'integer',
    ];

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the associated member
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * Get the valid members for this niche
     */
    public function validMembers()
    {
        return $this->hasMany(ValidMember::class, 'niche_id');
    }

    /**
     * Scope to get only available niches
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    /**
     * Scope to get niches by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope to search niches by various criteria
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('owner_name', 'like', "%{$search}%")
              ->orWhere('niche_no', 'like', "%{$search}%")
              ->orWhere('sr_no', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    /**
     * Get the full niche identifier
     */
    public function getFullIdentifierAttribute()
    {
        return "N{$this->niche_no}-{$this->sr_no}";
    }

    /**
     * Check if the niche is available for occupation
     */
    public function isAvailable()
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Mark the niche as unavailable
     */
    public function markAsUnavailable($occupationDate = null)
    {
        $this->update([
            'status' => 'unavailable',
            'last_occupation_date' => $occupationDate ?? now()->toDateString(),
        ]);
    }

    /**
     * Calculate total volume
     */
    public function getTotalVolumeAttribute()
    {
        if (!$this->size_width || !$this->size_height || !$this->size_depth) {
            return null;
        }

        return $this->size_width * $this->size_height * $this->size_depth;
    }
}
