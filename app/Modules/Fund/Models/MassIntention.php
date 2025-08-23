<?php

namespace Modules\Fund\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\Member;

class MassIntention extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'family_no',
        'member_id',
        'mass_schedule_id',
        'intention_type_id',
        'intention_for',
        'amount',
        'status',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function massSchedule()
    {
        return $this->belongsTo(MassSchedule::class);
    }

    public function intentionType()
    {
        return $this->belongsTo(IntentionType::class);
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
    public function getMemberDisplayAttribute()
    {
        return $this->member_id ? $this->member->name : 'Family Request';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
