<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\User;
use Modules\Graveyard\Models\TemporaryGrave;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Graveyard\Models\Niche;
use Modules\Graveyard\Models\ServiceType;
use Modules\Graveyard\Models\Payment;
use Modules\Members\Models\Relationship;
use Illuminate\Support\Facades\Auth;

class NicheTransfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transfer_reference',
        'status',
        'from_temporary_grave_id',
        'from_booking_id',
        'to_niche_id',
        'transfer_request_date',
        'proposed_transfer_date',
        'actual_transfer_date',
        'transfer_reason',
        'date_of_birth',
        'died_on',
        'buried_on',
        'applicant_name',
        'contact_no',
        'contact_email',
        'applicant_address',
        'relationship_id',
        'paid_amount',
        'balance_amount',
        'payment_status',
        'payment_method',
        'payment_remarks',
        'selected_services',
        'total_cost',
        'permit_no',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transfer_request_date' => \App\Casts\DateString::class,
        'proposed_transfer_date' => \App\Casts\DateString::class,
        'actual_transfer_date' => \App\Casts\DateString::class,
        'date_of_birth' => \App\Casts\DateString::class,
        'died_on' => \App\Casts\DateString::class,
        'buried_on' => \App\Casts\DateString::class,
        'required_documents' => 'array',
    ];

    /**
     * Boot method to generate transfer reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            if (empty($transfer->transfer_reference)) {
                $transfer->transfer_reference = static::generateTransferReference();
            }

            // Copy deceased details from original booking if not provided
            if ($transfer->from_booking_id && !$transfer->first_name) {
                $transfer->copyDeceasedDetails();
            }

            // Costs will be calculated later when approved/reviewed
            // Not calculating costs during initial request creation
        });

        // Handle status changes
        static::updated(function ($transfer) {
            if ($transfer->isDirty('status')) {
                $transfer->processTransferCompletion();
            }
        });
    }

    /**
     * Generate unique transfer reference
     */
    public static function generateTransferReference(): string
    {
        do {
            $reference = 'NT' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('transfer_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Get the source temporary grave
     */
    public function fromTemporaryGrave(): BelongsTo
    {
        return $this->belongsTo(TemporaryGrave::class, 'from_temporary_grave_id');
    }

    /**
     * Get the original temporary booking
     */
    public function fromBooking(): BelongsTo
    {
        return $this->belongsTo(TemporaryGraveBooking::class, 'from_booking_id');
    }

    /**
     * Get the destination niche
     */
    public function toNiche(): BelongsTo
    {
        return $this->belongsTo(Niche::class, 'to_niche_id');
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
     * Get the relationship
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class, 'relationship_id');
    }

    /**
     * Get the payments for this transfer
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending transfers
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }


    /**
     * Scope for transfers due soon
     */
    public function scopeDueSoon($query)
    {
        return $query->where('proposed_transfer_date', '<=', now()->addDays(7))
            ->whereIn('status', ['pending', 'approved']);
    }

    /**
     * Copy deceased details from original booking
     */
    protected function copyDeceasedDetails(): void
    {
        $booking = $this->fromBooking;
        if ($booking) {
            $this->dead_first_name = $booking->dead_first_name;
            $this->dead_last_name = $booking->dead_last_name;
            $this->date_of_birth = $booking->date_of_birth;
            $this->died_on = $booking->died_on;
            $this->buried_on = $booking->buried_on;
        }
    }

    /**
     * Process transfer completion
     */
    protected function processTransferCompletion(): void
    {
        // Mark temporary grave as available
        $this->fromTemporaryGrave->update([
            'last_burial_date' => null,
            'status' => 'available',
            'updated_by' => Auth::id()
        ]);

        // Mark niche as unavailable
        $this->toNiche->update([
            'status' => 'unavailable',
            'last_occupation_date' => $this->actual_transfer_date,
            'updated_by' => Auth::id()
        ]);

        // Mark original booking as completed
        $this->fromBooking->update([
            // 'status' => 'completed',
            'updated_by' => Auth::id()
        ]);
    }

    /**
     * Get the full name of the deceased
     */
    public function getFullNameAttribute(): string
    {
        return $this->dead_first_name . ' ' . $this->dead_last_name;
    }

    /**
     * Complete the transfer
     */
    public function complete(): bool
    {
        $this->update([
            'status' => 'completed',
            'actual_transfer_date' => now(),
            'updated_by' => Auth::id()
        ]);
        return true;
    }

    /**
     * Check if transfer can be completed
     */
    public function canBeCompleted(): bool
    {
        return true; // Allow completion without restrictions for now
    }
}
