<?php

namespace Modules\Fund\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\Member;

class FamilyContribution extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'family_no',
        'amount',
        'payment_method_id',
        'fund_category_id',
        'start_date',
        'end_date',
        'status',
        'member_id',
        'paid_by_name',
        'contact_no',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function fundCategory()
    {
        return $this->belongsTo(FundCategory::class);
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
    public function getPaidByDisplayAttribute()
    {
        return $this->member_id ? $this->member->name : $this->paid_by_name;
    }

    public function isPartialPayment()
    {
        return $this->status === 'partial';
    }

    public function isCompletePayment()
    {
        return $this->status === 'paid';
    }
}
