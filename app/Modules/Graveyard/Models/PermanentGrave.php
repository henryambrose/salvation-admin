<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Modules\Members\Models\User;
use Modules\Members\Models\Member;
use Carbon\Carbon;

class PermanentGrave extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grave_id',
        'section',
        'row_no',
        'grave_no',
        'oldno',
        'status',
        'last_burial_date',
        'owner_name',
        'member_id',
        'contact_no',
        'remarks',
        'plot_size',
        'is_active',
        'created_by',
        'updated_by',
        'pending_amount',
        'last_payment_year',
        'partial_payment_months',
    ];

    protected $casts = [
        'last_burial_date' => 'date',
        'plot_size' => 'decimal:2',
        'is_active' => 'boolean',
        'row_no' => 'integer',
        'grave_no' => 'integer',
        'pending_amount' => 'decimal:2',
        'partial_payment_months' => 'array',
    ];

    /**
     * Get the bookings for this permanent grave
     */

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the associated member
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * Get the valid members for this permanent grave
     */
    public function validMembers()
    {
        return $this->hasMany(ValidMember::class, 'permanent_grave_id');
    }

    /**
     * Get maintenance fee payments for this grave
     */
    public function maintenancePayments()
    {
        return $this->morphMany(Payment::class, 'payable')
            ->where('payment_notes', 'LIKE', '%maintenance%')
            ->orWhere('payment_notes', 'LIKE', '%annual%');
    }

    /**
     * Get the latest booking for this grave
     */


    /**
     * Scope to get only available graves
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    /**
     * Scope to get graves by section
     */
    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope to search graves by various criteria
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('owner_name', 'like', "%{$search}%")
                ->orWhere('grave_no', 'like', "%{$search}%")
                ->orWhere('section', 'like', "%{$search}%")
                ->orWhere('oldno', 'like', "%{$search}%")
                ->orWhere(DB::raw("CONCAT(section, '-', row_no, '-', grave_no)"), 'like', "%{$search}%")
                ->orWhereHas('member', function ($memberQuery) use ($search) {
                    $memberQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                });
        });
    }

    /**
     * Get the full grave identifier
     */
    public function getFullIdentifierAttribute()
    {
        return "{$this->section}-{$this->row_no}-{$this->grave_no}";
    }

    /**
     * Check if the grave is available for booking
     */
    public function isAvailable()
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Mark the grave as unavailable and update last burial date
     */
    public function markAsBooked($burialDate = null)
    {
        $this->update([
            'status' => 'unavailable',
            'last_burial_date' => $burialDate ?? now()->toDateString(),
        ]);
    }

    /**
     * Calculate pending maintenance amount based on years since last payment
     */
    public function calculatePendingAmount(): float
    {
        $annualFee = config('graveyard.annual_maintenance_fee', 5000);
        $currentYear = now()->year;

        // If no payment has been made, start from current year
        $lastPaymentYear = $this->last_payment_year ?? $currentYear;

        // Calculate years of pending payment
        $yearsSinceLastPayment = max(0, $currentYear - $lastPaymentYear);

        $totalPending = $yearsSinceLastPayment * $annualFee;

        // Subtract any partial payments for current year
        $currentYearPartialPayments = $this->getPartialPaymentsForYear($currentYear);
        if ($currentYearPartialPayments > 0) {
            $totalPending -= $currentYearPartialPayments;
        }

        return max(0, $totalPending);
    }

    /**
     * Get partial payments made for a specific year
     */
    public function getPartialPaymentsForYear(int $year): float
    {
        $partialPayments = $this->partial_payment_months ?? [];
        $yearPayments = $partialPayments[$year] ?? [];

        if (empty($yearPayments)) {
            return 0;
        }

        $annualFee = config('graveyard.annual_maintenance_fee', 5000);
        $monthlyFee = $annualFee / 12;

        // Calculate total amount paid for this year
        $totalMonthsPaid = count($yearPayments);

        return $totalMonthsPaid * $monthlyFee;
    }

    /**
     * Record a maintenance fee payment
     */
    public function recordMaintenancePayment(float $amount, array $monthsFor = null): void
    {
        $currentYear = now()->year;
        $annualFee = config('graveyard.annual_maintenance_fee', 5000);
        $monthlyFee = $annualFee / 12;

        // If no specific months provided, calculate based on amount
        if (!$monthsFor) {
            $monthsToPay = min(12, (int) round($amount / $monthlyFee));
            $monthsFor = range(1, $monthsToPay);
        }

        // Update partial payment months
        $partialPayments = $this->partial_payment_months ?? [];
        $yearPayments = $partialPayments[$currentYear] ?? [];

        // Add new months (avoid duplicates)
        foreach ($monthsFor as $month) {
            if (!in_array($month, $yearPayments)) {
                $yearPayments[] = $month;
            }
        }

        sort($yearPayments);
        $partialPayments[$currentYear] = $yearPayments;

        // Check if full year is paid
        if (count($yearPayments) >= 12) {
            $this->last_payment_year = $currentYear;
            // Clear partial payments for this year since it's fully paid
            unset($partialPayments[$currentYear]);
        }

        // Update the model
        $this->update([
            'partial_payment_months' => $partialPayments,
            'last_payment_year' => $this->last_payment_year,
            'pending_amount' => $this->calculatePendingAmount(),
        ]);
    }

    /**
     * Check if maintenance fee is fully paid for a given year
     */
    public function isMaintenanceFeePaidForYear(int $year): bool
    {
        if ($this->last_payment_year && $this->last_payment_year >= $year) {
            return true;
        }

        $partialPayments = $this->partial_payment_months ?? [];
        $yearPayments = $partialPayments[$year] ?? [];

        return count($yearPayments) >= 12;
    }

    /**
     * Get maintenance fee payment status
     */
    public function getMaintenancePaymentStatus(): string
    {
        $pendingAmount = $this->calculatePendingAmount();

        if ($pendingAmount <= 0) {
            return 'paid';
        }

        $currentYear = now()->year;
        $currentYearPartial = $this->getPartialPaymentsForYear($currentYear);

        if ($currentYearPartial > 0) {
            return 'partial';
        }

        return 'pending';
    }

    /**
     * Get formatted pending amount
     */
    public function getFormattedPendingAmountAttribute(): string
    {
        return '₹ ' . number_format($this->calculatePendingAmount(), 2);
    }

    /**
     * Scope to filter graves with pending maintenance payments
     */
    public function scopeWithPendingMaintenance($query)
    {
        return $query->whereRaw('
            (YEAR(NOW()) - COALESCE(last_payment_year, YEAR(NOW()))) * ? > COALESCE(pending_amount, 0)
        ', [config('graveyard.annual_maintenance_fee', 5000)]);
    }

    /**
     * Scope to filter by maintenance payment status
     */
    public function scopeByMaintenanceStatus($query, string $status)
    {
        $currentYear = now()->year;
        $annualFee = config('graveyard.annual_maintenance_fee', 5000);

        switch ($status) {
            case 'pending':
                return $query->whereRaw('
                    (? - COALESCE(last_payment_year, ?)) * ? > 0
                ', [$currentYear, $currentYear, $annualFee]);

            case 'paid':
                return $query->where('last_payment_year', '>=', $currentYear);

            case 'partial':
                return $query->whereRaw('
                    (? - COALESCE(last_payment_year, ?)) * ? > 0
                    AND partial_payment_months IS NOT NULL
                ', [$currentYear, $currentYear, $annualFee]);

            default:
                return $query;
        }
    }
}
