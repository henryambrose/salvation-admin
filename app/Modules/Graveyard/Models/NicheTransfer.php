<?php

namespace Modules\Graveyard\Models;

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
        'transfer_request_date' => 'date',
        'proposed_transfer_date' => 'date',
        'actual_transfer_date' => 'date',
        'date_of_birth' => 'date',
        'died_on' => 'date',
        'buried_on' => 'date',
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
     * Process status changes
     */
    // protected function processStatusChange(): void
    // {
    //     switch ($this->status) {
    //         case 'approved':
    //             $this->approval_date = now();
    //             $this->approved_by = Auth::id();
    //             break;

    //         case 'rejected':
    //             $this->rejection_date = now();
    //             $this->rejected_by = Auth::id();
    //             break;

    //         case 'completed':
    //             $this->actual_transfer_date = now();
    //             $this->processTransferCompletion();
    //             break;
    //     }
    // }

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
            'status' => 'completed',
            'updated_by' => Auth::id()
        ]);
    }

    /**
     * Approve the transfer
     */
    // public function approve(string $notes): bool
    // {
    //     if ($this->status !== 'pending') {
    //         return false;
    //     }

    //     $this->update([
    //         'status' => 'approved',
    //         'admin_notes' => $notes,
    //     ]);

    //     return true;
    // }

    /**
     * Reject the transfer
     */
    // public function reject(string $reason): bool
    // {
    //     if ($this->status !== 'pending') {
    //         return false;
    //     }

    //     $this->update([
    //         'status' => 'rejected',
    //         'rejection_reason' => $reason,
    //     ]);

    //     return true;
    // }

    /**
     * Complete the transfer
     */
    public function complete(): bool
    {
        if ($this->status !== 'approved') {
            return false;
        }

        $this->update(['status' => 'completed']);
        return true;
    }

    /**
     * Check if transfer can be completed
     */
    // public function canBeCompleted(): bool
    // {
    //     return $this->status === 'approved' &&
    //         $this->payment_status === 'paid' &&
    //         $this->proposed_transfer_date <= now();
    // }

    /**
     * Get status color for UI
     */
    // public function getStatusColorAttribute(): string
    // {
    //     return match ($this->status) {
    //         'pending' => 'bg-yellow-100 text-yellow-800',
    //         'approved' => 'bg-blue-100 text-blue-800',
    //         'rejected' => 'bg-red-100 text-red-800',
    //         'completed' => 'bg-green-100 text-green-800',
    //         'cancelled' => 'bg-gray-100 text-gray-800',
    //         default => 'bg-gray-100 text-gray-800'
    //     };
    // }
}
