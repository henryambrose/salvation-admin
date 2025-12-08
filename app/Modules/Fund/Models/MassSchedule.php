<?php

namespace Modules\Fund\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MassSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mass_date',
        'mass_time',
        'max_intentions',
        'current_intentions',
        'is_active',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'mass_date' => \App\Casts\DateString::class,
        'mass_time' => 'datetime:H:i:s',
        'max_intentions' => 'integer',
        'current_intentions' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function massIntentions()
    {
        return $this->hasMany(MassIntention::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Helper methods
    public function getAvailableSlotsAttribute()
    {
        return $this->max_intentions - $this->current_intentions;
    }

    public function isFull()
    {
        return $this->current_intentions >= $this->max_intentions;
    }

    public function hasAvailableSlots()
    {
        return $this->current_intentions < $this->max_intentions;
    }
}
