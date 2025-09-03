<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

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
        'deceased_first_name',
        'deceased_last_name',
        'deceased_date_of_birth',
        'deceased_died_on',
        'deceased_buried_on',
        'transfer_applicant_name',
        'transfer_contact_no',
        'transfer_contact_email',
        'relationship_to_deceased',
        'applicant_address',
        'selected_services',
        'transfer_cost',
        'niche_cost',
        'total_cost',
        'paid_amount',
        'balance_amount',
        'payment_status',
        'payment_method',
        'payment_remarks',
        'admin_notes',
        'approved_by',
        'approval_date',
        'rejection_reason',
        'rejected_by',
        'rejection_date',
        'transfer_permit_no',
        'required_documents',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transfer_request_date' => 'date',
        'proposed_transfer_date' => 'date',
        'actual_transfer_date' => 'date',
        'deceased_date_of_birth' => 'date',
        'deceased_died_on' => 'date',
        'deceased_buried_on' => 'date',
        'approval_date' => 'date',
        'rejection_date' => 'date',
        'selected_services' => 'array',
        'required_documents' => 'array',
        'transfer_cost' => 'decimal:2',
        'niche_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
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
            if ($transfer->from_booking_id && !$transfer->deceased_first_name) {
                $transfer->copyDeceasedDetails();
            }

            // Calculate costs
            if (!$transfer->total_cost) {
                $transfer->calculateTotalCost();
            }
        });

        // Handle status changes
        static::updated(function ($transfer) {
            if ($transfer->isDirty('status')) {
                $transfer->processStatusChange();
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
     * Get the approver
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the rejecter
     */
    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
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
     * Scope for approved transfers
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
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
            $this->deceased_first_name = $booking->dead_first_name;
            $this->deceased_last_name = $booking->dead_last_name;
            $this->deceased_date_of_birth = $booking->date_of_birth;
            $this->deceased_died_on = $booking->died_on;
            $this->deceased_buried_on = $booking->buried_on;
        }
    }

    /**
     * Calculate total cost
     */
    protected function calculateTotalCost(): void
    {
        // Get niche cost
        if ($this->to_niche_id && !$this->niche_cost) {
            $niche = Niche::find($this->to_niche_id);
            $this->niche_cost = $niche ? $niche->cost : 0;
        }

        // Calculate transfer service cost based on selected services
        $serviceCost = 0;
        if ($this->selected_services) {
            $serviceIds = is_array($this->selected_services) ? $this->selected_services : [];
            $serviceCost = ServiceType::whereIn('id', $serviceIds)->sum('cost');
        }
        $this->transfer_cost = $serviceCost;

        // Total cost
        $this->total_cost = $this->niche_cost + $this->transfer_cost;
        $this->balance_amount = $this->total_cost - $this->paid_amount;
    }

    /**
     * Get deceased full name
     */
    public function getDeceasedFullNameAttribute(): string
    {
        return trim("{$this->deceased_first_name} {$this->deceased_last_name}");
    }

    /**
     * Process status changes
     */
    protected function processStatusChange(): void
    {
        switch ($this->status) {
            case 'approved':
                $this->approval_date = now();
                $this->approved_by = auth()->id();
                break;

            case 'rejected':
                $this->rejection_date = now();
                $this->rejected_by = auth()->id();
                break;

            case 'completed':
                $this->actual_transfer_date = now();
                $this->processTransferCompletion();
                break;
        }
    }

    /**
     * Process transfer completion
     */
    protected function processTransferCompletion(): void
    {
        // Mark temporary grave as available
        $this->fromTemporaryGrave->update([
            'is_available' => true,
            'occupied_date' => null,
            'updated_by' => auth()->id()
        ]);

        // Mark niche as occupied
        $this->toNiche->update([
            'is_available' => false,
            'occupied_date' => $this->actual_transfer_date,
            'updated_by' => auth()->id()
        ]);

        // Mark original booking as completed
        $this->fromBooking->update([
            'status' => 'completed',
            'updated_by' => auth()->id()
        ]);
    }

    /**
     * Approve the transfer
     */
    public function approve(string $notes = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'approved',
            'admin_notes' => $notes,
        ]);

        return true;
    }

    /**
     * Reject the transfer
     */
    public function reject(string $reason): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        return true;
    }

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
    public function canBeCompleted(): bool
    {
        return $this->status === 'approved' && 
               $this->payment_status === 'paid' && 
               $this->proposed_transfer_date <= now();
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}