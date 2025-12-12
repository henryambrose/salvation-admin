<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\User;
use Modules\Members\Models\Member;
use Modules\Graveyard\Models\AnnualMaintenanceFee;

class Niche extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'niche_no',
        'sr_no',
        'location',
        'status',
        'last_occupation_date',
        'owner_name',
        'member_id',
        'contact_no',
        'remarks',
        'size_width',
        'size_height',
        'size_depth',
        'is_active',
        'created_by',
        'updated_by',
        'pending_amount',
        'last_payment_year',
        'partial_payment_months',
    ];

    protected $casts = [
        'last_occupation_date' => \App\Casts\DateString::class,
        'size_width' => 'decimal:2',
        'size_height' => 'decimal:2',
        'size_depth' => 'decimal:2',
        'is_active' => 'boolean',
        'niche_no' => 'integer',
        'sr_no' => 'integer',
        'pending_amount' => 'decimal:2',
        'partial_payment_months' => 'array',
    ];

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
     * Get the valid members for this niche
     */
    public function validMembers()
    {
        return $this->hasMany(ValidMember::class, 'niche_id');
    }

    /**
     * Scope to get only available niches
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    /**
     * Scope to get niches by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope to search niches by various criteria
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('owner_name', 'like', "%{$search}%")
              ->orWhere('niche_no', 'like', "%{$search}%")
              ->orWhere('sr_no', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    /**
     * Get the full niche identifier
     */
    public function getFullIdentifierAttribute()
    {
        return "N{$this->niche_no}-{$this->sr_no}";
    }

    /**
     * Check if the niche is available for occupation
     */
    public function isAvailable()
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Mark the niche as unavailable
     */
    public function markAsUnavailable($occupationDate = null)
    {
        $this->update([
            'status' => 'unavailable',
            'last_occupation_date' => $occupationDate ?? now()->toDateString(),
        ]);
    }

    /**
     * Calculate total volume
     */
    public function getTotalVolumeAttribute()
    {
        if (!$this->size_width || !$this->size_height || !$this->size_depth) {
            return null;
        }

        return $this->size_width * $this->size_height * $this->size_depth;
    }

    /**
     * Get maintenance fee payments for this niche
     */
    public function maintenancePayments()
    {
        return $this->morphMany(Payment::class, 'payable')
            ->where('payment_notes', 'LIKE', '%maintenance%')
            ->orWhere('payment_notes', 'LIKE', '%annual%');
    }

    /**
     * Calculate pending maintenance amount based on years since last payment
     */
    public function calculatePendingAmount(): float
    {
        return $this->calculatePendingAmountWithHistoricalRates();
    }

    /**
     * Calculate pending maintenance amount using historical rates
     */
    public function calculatePendingAmountWithHistoricalRates(): float
    {
        $currentYear = now()->year;
        $lastPaymentYear = $this->last_payment_year;

        // If no payment has been made, determine starting year
        if (!$lastPaymentYear) {
            // Start from the year when this niche was created or current year - 1
            $createdYear = $this->created_at ? $this->created_at->year : $currentYear;
            $lastPaymentYear = min($createdYear, $currentYear) - 1;
        }

        // If fully paid up to current year, no pending amount
        if ($lastPaymentYear >= $currentYear) {
            return 0;
        }

        $totalPending = 0;

        // Calculate year by year with historical rates
        for ($year = $lastPaymentYear + 1; $year <= $currentYear; $year++) {
            $yearlyFee = $this->getAnnualMaintenanceFeeForYear($year);
            $totalPending += $yearlyFee;
        }

        // Subtract any partial payments for current year
        $currentYearPartial = $this->getPartialPaymentsForYear($currentYear);
        $totalPending -= $currentYearPartial;

        return max(0, $totalPending);
    }

    /**
     * Get the annual maintenance fee for a specific year
     */
    private function getAnnualMaintenanceFeeForYear(int $year): float
    {
        // Try to get historical rate from annual_maintenance_fees table
        $historicalRate = AnnualMaintenanceFee::getFeeForYearAndType($year, 'niche');

        // Fallback to config if no historical rate found
        return $historicalRate ?? config('graveyard.annual_maintenance_fee', 3000);
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

        $annualFee = $this->getAnnualMaintenanceFeeForYear($year);
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
        $annualFee = $this->getAnnualMaintenanceFeeForYear($currentYear);
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

        // Update the model with rounded pending amount
        $newPendingAmount = $this->calculatePendingAmount();

        $this->update([
            'partial_payment_months' => $partialPayments,
            'last_payment_year' => $this->last_payment_year,
            'pending_amount' => $newPendingAmount,
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
     * Scope to filter niches with pending maintenance payments
     */
    public function scopeWithPendingMaintenance($query)
    {
        return $query->whereRaw('
            (YEAR(NOW()) - COALESCE(last_payment_year, YEAR(NOW()))) * ? > COALESCE(pending_amount, 0)
        ', [config('graveyard.annual_maintenance_fee', 3000)]);
    }

    /**
     * Scope to filter by maintenance payment status
     */
    public function scopeByMaintenanceStatus($query, string $status)
    {
        $currentYear = now()->year;
        $annualFee = config('graveyard.annual_maintenance_fee', 3000);

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
