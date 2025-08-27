<?php

namespace Modules\Fund\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\Member;
use Modules\Fund\Models\MassType;
use Modules\Fund\Models\MassIntentionType;
use Modules\Fund\Models\PaymentMethod;
use Modules\Fund\Models\MassSchedule;
use App\Models\User;

class MassIntention extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'non_member_name',
        'phone',
        'mass_date',
        'mass_type_id',
        'mass_intention_type_id',
        'special_instructions',
        'payment_method_id',
        'amount',
        'intention_for',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'mass_date' => 'date',
        'amount' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Get the member for this mass intention
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the mass type for this mass intention
     */
    public function massType()
    {
        return $this->belongsTo(MassType::class);
    }

    /**
     * Get the mass intention type for this mass intention
     */
    public function massIntentionType()
    {
        return $this->belongsTo(MassIntentionType::class, 'mass_intention_type_id');
    }

    /**
     * Alias for massIntentionType relationship
     */
    public function intentionType()
    {
        return $this->massIntentionType();
    }

    /**
     * Get the payment method for this mass intention
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the mass schedule for this mass intention
     */
    // public function massSchedule()
    // {
    //     return $this->belongsTo(MassSchedule::class, 'mass_schedule_id');
    // }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
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
