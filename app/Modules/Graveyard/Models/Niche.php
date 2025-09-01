<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Niche extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'niche_no',
        'sr_no',
        'location',
        'status',
        'last_occupation_date',
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
     * Mark the niche as occupied
     */
    public function markAsOccupied($occupationDate = null)
    {
        $this->update([
            'status' => 'occupied',
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
