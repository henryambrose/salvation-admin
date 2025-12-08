<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Graveyard\Models\ValidMember;

class PermanentGraveBooking extends Model
{
    use SoftDeletes, Auditable;

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
        'booking_date' => \App\Casts\DateString::class,
        'died_on' => \App\Casts\DateString::class,
        'buried_on' => \App\Casts\DateString::class,
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

            // Fire event when payment status changes to paid/completed
            if ($booking->isDirty('payment_status') && in_array($booking->payment_status, ['paid', 'completed'])) {
                event(new \App\Events\PaymentCompleted($booking));
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
     * Get the payments for this booking
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Get the latest payment
     */
    public function latestPayment()
    {
        return $this->morphOne(Payment::class, 'payable')->latest();
    }

    /**
     * Check if booking has completed payment
     */
    public function hasCompletedPayment(): bool
    {
        return $this->payments()->completed()->exists();
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAmount(): float
    {
        return $this->payments()->sum('paid_amount');
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
        // Mark permanent grave as unavailable and update burial date
        if ($this->permanentGrave) {
            $this->permanentGrave->markAsBooked($this->buried_on);
        }

        // Mark valid member as deceased
        if ($this->validMember) {
            $this->validMember->update([
                'death_date' => $this->died_on,
                'updated_by' => Auth::id()
            ]);
        }
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
     * Get the obituary page for this booking
     */
    public function obituaryPage(): HasOne
    {
        return $this->hasOne(\Modules\Graveyard\Models\ObituaryPage::class);
    }

    /**
     * Check if this booking has an obituary page
     */
    public function hasObituaryPage(): bool
    {
        return $this->obituaryPage()->exists();
    }

    /**
     * Cancel the booking
     */
    public function cancel($reason = null): bool
    {
        $this->update([
            'status' => 'cancelled',
            'remarks' => $reason
        ]);

        // For permanent graves, we might want to reset the grave status if this was a confirmed booking
        // This depends on business rules - permanent graves might stay unavailable even after cancellation
        // Uncomment below if graves should be released on cancellation:
        /*
        if ($this->permanentGrave && $this->permanentGrave->status === 'unavailable') {
            $this->permanentGrave->update(['status' => 'available']);
        }
        */

        return true;
    }
}
