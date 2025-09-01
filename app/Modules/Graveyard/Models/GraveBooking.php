<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Members\Models\Member;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Parish;
use Carbon\Carbon;

class GraveBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grave_type',
        'permanent_grave_id',
        'temporary_grave_id',
        'dead_first_name',
        'dead_last_name',
        'date_of_birth',
        'age',
        'months',
        'days',
        'died_on',
        'buried_on',
        'gender_id',
        'cause_of_death',
        'nationality',
        'remarks',
        'parish_id',
        'minister',
        'relationship',
        'applicant_type',
        'member_id',
        'applicant_member_id',
        'applicant_name',
        'contact_no',
        'permit_no',
        'status',
        'total_amount',
        'payment_method_id',
        'payment_remarks',
        'payment_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'died_on' => 'date',
        'buried_on' => 'date',
        'total_amount' => 'decimal:2',
        'age' => 'integer',
        'months' => 'integer',
        'days' => 'integer',
    ];

    /**
     * Get the permanent grave for this booking
     */
    public function permanentGrave()
    {
        return $this->belongsTo(PermanentGrave::class);
    }

    /**
     * Get the temporary grave for this booking
     */
    public function temporaryGrave()
    {
        return $this->belongsTo(TemporaryGrave::class);
    }

    /**
     * Get the grave (either permanent or temporary)
     */
    public function grave()
    {
        if ($this->grave_type === 'permanent') {
            return $this->permanentGrave;
        } else {
            return $this->temporaryGrave;
        }
    }

    /**
     * Get the deceased member if applicable
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the applicant member if applicable
     */
    public function applicantMember()
    {
        return $this->belongsTo(Member::class, 'applicant_member_id');
    }

    /**
     * Get the gender of the deceased
     */
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    /**
     * Get the parish
     */
    public function parish()
    {
        return $this->belongsTo(Parish::class, 'parish_id');
    }

    /**
     * Get the booking services
     */
    public function bookingServices()
    {
        return $this->hasMany(BookingService::class, 'booking_id');
    }

    /**
     * Get the user who created this booking
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this booking
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to filter by grave type
     */
    public function scopeByGraveType($query, $type)
    {
        return $query->where('grave_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by payment status
     */
    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope to search by deceased name
     */
    public function scopeSearchDeceased($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('dead_first_name', 'like', "%{$search}%")
              ->orWhere('dead_last_name', 'like', "%{$search}%")
              ->orWhere('permit_no', 'like', "%{$search}%");
        });
    }

    /**
     * Get the full name of the deceased
     */
    public function getDeceasedFullNameAttribute()
    {
        return trim("{$this->dead_first_name} {$this->dead_last_name}");
    }

    /**
     * Get the applicant name (member or non-member)
     */
    public function getApplicantNameAttribute()
    {
        if ($this->applicant_type === 'member' && $this->applicantMember) {
            return $this->applicantMember->first_name . ' ' . $this->applicantMember->last_name;
        }
        return $this->attributes['applicant_name'] ?? 'N/A';
    }

    /**
     * Calculate age from date of birth
     */
    public function calculateAgeFromDob()
    {
        if (!$this->date_of_birth) {
            return null;
        }

        $dob = Carbon::parse($this->date_of_birth);
        $deathDate = $this->died_on ? Carbon::parse($this->died_on) : now();
        
        $age = $dob->diffInYears($deathDate);
        $months = $dob->copy()->addYears($age)->diffInMonths($deathDate);
        $days = $dob->copy()->addYears($age)->addMonths($months)->diffInDays($deathDate);

        return [
            'years' => $age,
            'months' => $months,
            'days' => $days
        ];
    }

    /**
     * Calculate date of birth from age, months, days
     */
    public function calculateDobFromAge()
    {
        if (!$this->age && !$this->months && !$this->days) {
            return null;
        }

        $deathDate = $this->died_on ? Carbon::parse($this->died_on) : now();
        
        return $deathDate->copy()
            ->subYears($this->age ?? 0)
            ->subMonths($this->months ?? 0)
            ->subDays($this->days ?? 0);
    }

    /**
     * Update the age fields based on date of birth
     */
    public function updateAgeFromDob()
    {
        $calculated = $this->calculateAgeFromDob();
        if ($calculated) {
            $this->age = $calculated['years'];
            $this->months = $calculated['months'];
            $this->days = $calculated['days'];
        }
    }

    /**
     * Calculate total amount from services
     */
    public function calculateTotalAmount()
    {
        $total = $this->bookingServices()->sum('amount');
        
        // Apply concessions and free services logic
        $hasFreeService = $this->bookingServices()
            ->whereHas('serviceType', function ($query) {
                $query->where('type', 'free');
            })->exists();

        if ($hasFreeService) {
            return 0;
        }

        $concessionAmount = $this->bookingServices()
            ->whereHas('serviceType', function ($query) {
                $query->where('type', 'concession');
            })->sum('amount');

        return max(0, $total - $concessionAmount);
    }

    /**
     * Update the total amount
     */
    public function updateTotalAmount()
    {
        $this->total_amount = $this->calculateTotalAmount();
        $this->save();
    }

    /**
     * Mark the booking as confirmed and update grave status
     */
    public function confirm()
    {
        $this->status = 'confirmed';
        $this->save();

        // Update grave status
        $grave = $this->grave();
        if ($grave) {
            $grave->markAsBooked($this->buried_on);
        }
    }
}
