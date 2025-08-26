<?php

namespace Modules\Fund\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MassType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mass_types';

    protected $fillable = [
        'name',
        'description',
        'default_time',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'default_time' => 'datetime:H:i:s',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the mass schedules for this mass type
     */
    public function massSchedules()
    {
        return $this->hasMany(MassSchedule::class);
    }

    /**
     * Get the mass intentions for this mass type
     */
    public function massIntentions()
    {
        return $this->hasMany(MassIntention::class);
    }

    /**
     * Get the user who created this mass type
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the user who last updated this mass type
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Scope to get only active mass types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
