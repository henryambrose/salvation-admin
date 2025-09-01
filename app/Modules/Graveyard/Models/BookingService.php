<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'service_type_id',
        'amount',
        'original_cost',
        'quantity',
        'remarks',
        'is_complimentary',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'original_cost' => 'decimal:2',
        'quantity' => 'integer',
        'is_complimentary' => 'boolean',
    ];

    /**
     * Get the booking this service belongs to
     */
    public function booking()
    {
        return $this->belongsTo(GraveBooking::class);
    }

    /**
     * Get the service type
     */
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Get the total amount (amount * quantity)
     */
    public function getTotalAmountAttribute()
    {
        return $this->amount * $this->quantity;
    }

    /**
     * Check if this is a concession service
     */
    public function isConcession()
    {
        return $this->serviceType && $this->serviceType->type === 'concession';
    }

    /**
     * Check if this is a free service
     */
    public function isFree()
    {
        return $this->serviceType && $this->serviceType->type === 'free';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    /**
     * Scope to get only normal services (not concession or free)
     */
    public function scopeNormalServices($query)
    {
        return $query->whereHas('serviceType', function ($q) {
            $q->where('type', 'normal');
        });
    }

    /**
     * Scope to get concession services
     */
    public function scopeConcessionServices($query)
    {
        return $query->whereHas('serviceType', function ($q) {
            $q->where('type', 'concession');
        });
    }

    /**
     * Scope to get free services
     */
    public function scopeFreeServices($query)
    {
        return $query->whereHas('serviceType', function ($q) {
            $q->where('type', 'free');
        });
    }
}
