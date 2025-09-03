<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Carbon\Carbon;

class PermanentGraveBooking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'status',
        'permanent_grave_id',
        'valid_member_id',
        'booking_date',
        'died_on',
        'buried_on',
        'cause_of_death',
        'minister',
        'special_requirements',
        'remarks',
        'applicant_type',
        'applicant_name',
        'contact_no',
        'contact_email',
        'permit_no',
        'selected_services',
        'total_cost',
        'paid_amount',
        'balance_amount',
        'payment_status',
        'payment_method',
        'payment_remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'died_on' => 'date',
        'buried_on' => 'date',
        'selected_services' => 'array',
        'total_cost' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    /**
     * Boot method to generate booking reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = static::generateBookingReference();
            }
        });

        // When confirmed, update grave last burial date and mark valid member as deceased
        static::updated(function ($booking) {
            if ($booking->isDirty('status') && $booking->status === 'confirmed') {
                $booking->processConfirmation();
            }
        });
    }

    /**
     * Generate unique booking reference
     */
    public static function generateBookingReference(): string
    {
        do {
            $reference = 'PGB' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('booking_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Get the permanent grave
     */
    public function permanentGrave(): BelongsTo
    {
        return $this->belongsTo(PermanentGrave::class);
    }

    /**
     * Get the valid member
     */
    public function validMember(): BelongsTo
    {
        return $this->belongsTo(ValidMember::class);
    }

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Check if booking can be confirmed (24-month rule)
     */
    public function canBeConfirmed(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Check if grave is eligible (24-month rule)
        $grave = $this->permanentGrave;
        if (!$grave->last_burial_date) {
            return true; // Never used before
        }

        $monthsSinceLastBurial = Carbon::parse($grave->last_burial_date)->diffInMonths(now());
        return $monthsSinceLastBurial >= 24;
    }

    /**
     * Get eligibility message
     */
    public function getEligibilityMessage(): string
    {
        $grave = $this->permanentGrave;
        
        if (!$grave->last_burial_date) {
            return 'Grave is eligible - never been used before.';
        }

        $monthsSinceLastBurial = Carbon::parse($grave->last_burial_date)->diffInMonths(now());
        
        if ($monthsSinceLastBurial >= 24) {
            return "Grave is eligible - {$monthsSinceLastBurial} months since last burial.";
        } else {
            $remainingMonths = 24 - $monthsSinceLastBurial;
            return "Grave not eligible - {$remainingMonths} months remaining.";
        }
    }

    /**
     * Process booking confirmation
     */
    public function processConfirmation(): void
    {
        // Update grave's last burial date
        $this->permanentGrave->update([
            'last_burial_date' => $this->buried_on,
            'updated_by' => auth()->id()
        ]);

        // Mark valid member as deceased
        $this->validMember->update([
            'death_date' => $this->died_on,
            'updated_by' => auth()->id()
        ]);
    }

    /**
     * Calculate balance amount
     */
    public function calculateBalance(): void
    {
        $this->balance_amount = $this->total_cost - $this->paid_amount;
        
        if ($this->paid_amount == 0) {
            $this->payment_status = 'pending';
        } elseif ($this->paid_amount >= $this->total_cost) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }
    }

    /**
     * Get deceased person's name
     */
    public function getDeceasedNameAttribute(): string
    {
        return $this->validMember ? $this->validMember->full_name : 'Unknown';
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Check if booking is overdue
     */
    public function isOverdue(): bool
    {
        return $this->buried_on < now()->toDateString() && !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Confirm the booking
     */
    public function confirm(): bool
    {
        if (!$this->canBeConfirmed()) {
            return false;
        }

        $this->update(['status' => 'confirmed']);
        return true;
    }

    /**
     * Cancel the booking
     */
    public function cancel(string $reason = null): bool
    {
        $this->update([
            'status' => 'cancelled',
            'remarks' => $reason
        ]);

        return true;
    }
}