<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\User;
use Modules\Members\Models\Member;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Parish;
use Modules\Members\Models\Relationship;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemporaryGraveBooking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'status',
        'temporary_grave_id',
        'booking_date',
        'died_on',
        'buried_on',
        'dead_first_name',
        'dead_last_name',
        'date_of_birth',
        'age',
        'months',
        'days',
        'gender_id',
        'deceased_member_id',
        'nationality',
        'parish_id',
        'cause_of_death',
        'minister',
        'special_requirements',
        'remarks',
        'applicant_type',
        'applicant_member_id',
        'applicant_name',
        'contact_no',
        'contact_email',
        'relationship_id',
        'permit_no',
        'selected_services',
        'total_cost',
        'paid_amount',
        'balance_amount',
        'payment_status',
        'payment_method',
        'payment_remarks',
        'expected_transfer_date',
        'transfer_requested',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'died_on' => 'date',
        'buried_on' => 'date',
        'date_of_birth' => 'date',
        'expected_transfer_date' => 'date',
        'selected_services' => 'array',
        'total_cost' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'age' => 'integer',
        'months' => 'integer',
        'days' => 'integer',
        'transfer_requested' => 'boolean',
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

            // Calculate expected transfer date if not set
            if (!$booking->expected_transfer_date && $booking->buried_on) {
                $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
                $booking->expected_transfer_date = Carbon::parse($booking->buried_on)
                    ->addMonths($monthsFromEnv);
            }
        });

        // When confirmed, mark temporary grave as occupied
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
            $reference = 'TGB' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('booking_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Get the temporary grave
     */
    public function temporaryGrave(): BelongsTo
    {
        return $this->belongsTo(TemporaryGrave::class);
    }

    /**
     * Get the gender
     */
    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    /**
     * Get the parish
     */
    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    /**
     * Get the applicant member (if applicant is a member)
     */
    public function applicantMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'applicant_member_id');
    }

    /**
     * Get the deceased member (if deceased is a member)
     */
    public function deceasedMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'deceased_member_id');
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
        return $this->belongsTo(Relationship::class);
    }

    /**
     * Get niche transfers from this booking
     */
    public function nicheTransfers(): HasMany
    {
        return $this->hasMany(NicheTransfer::class, 'from_booking_id');
    }

    /**
     * Get payments for this booking (polymorphic)
     */
    public function payments(): HasMany
    {
        return $this->hasMany(\Modules\Graveyard\Models\Payment::class, 'payable_id')->where('payable_type', self::class);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for bookings eligible for transfer
     */
    public function scopeEligibleForTransfer($query)
    {
        $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
        return $query->where('status', 'confirmed')
            ->whereRaw('DATE_ADD(buried_on, INTERVAL ? MONTH) <= CURDATE()', [$monthsFromEnv]);
    }

    /**
     * Scope to search by deceased name and related fields
     */
    public function scopeSearchDeceased($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('dead_first_name', 'like', "%{$search}%")
                ->orWhere('dead_last_name', 'like', "%{$search}%")
                ->orWhere('permit_no', 'like', "%{$search}%")
                ->orWhere('booking_reference', 'like', "%{$search}%")
                ->orWhere('applicant_name', 'like', "%{$search}%")
                ->orWhere('contact_no', 'like', "%{$search}%")
                ->orWhereHas('temporaryGrave', function ($graveQuery) use ($search) {
                    $graveQuery->where('grave_no', 'like', "%{$search}%")
                        ->orWhere('section', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Get the full name of the deceased
     */
    public function getDeceasedFullNameAttribute(): string
    {
        return trim("{$this->dead_first_name} {$this->dead_last_name}");
    }

    /**
     * Get formatted applicant name
     */
    public function getApplicantNameAttribute(): string
    {
        if ($this->applicant_type === 'member' && $this->applicantMember) {
            return $this->applicantMember->first_name . ' ' . $this->applicantMember->last_name;
        }
        return $this->attributes['applicant_name'] ?? 'N/A';
    }

    /**
     * Calculate age from date of birth or age fields
     */
    public function getCalculatedAgeAttribute(): array
    {
        if ($this->date_of_birth) {
            $dob = Carbon::parse($this->date_of_birth);
            $deathDate = Carbon::parse($this->died_on);

            $years = $dob->diffInYears($deathDate);
            $months = $dob->copy()->addYears($years)->diffInMonths($deathDate);
            $days = $dob->copy()->addYears($years)->addMonths($months)->diffInDays($deathDate);

            return [
                'years' => $years,
                'months' => $months,
                'days' => $days
            ];
        }

        return [
            'years' => $this->age ?? 0,
            'months' => $this->months ?? 0,
            'days' => $this->days ?? 0
        ];
    }

    /**
     * Process booking confirmation
     */
    public function processConfirmation(): void
    {
        // Mark temporary grave as unavailable using the proper method
        if ($this->temporaryGrave) {
            $this->temporaryGrave->markAsBooked($this->buried_on);
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
     * Check if transfer is due soon
     */
    public function isTransferDueSoon(): bool
    {
        if (!$this->expected_transfer_date) {
            return false;
        }

        $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
        return $this->expected_transfer_date <= now()->addMonths($monthsFromEnv);
    }

    /**
     * Check if transfer is overdue
     */
    public function isTransferOverdue(): bool
    {
        if (!$this->expected_transfer_date) {
            return false;
        }

        return $this->expected_transfer_date < now();
    }

    /**
     * Request transfer to niche
     */
    public function requestTransfer(): bool
    {
        if ($this->status !== 'confirmed' || $this->transfer_requested) {
            return false;
        }

        $this->update(['transfer_requested' => true]);
        return true;
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
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
