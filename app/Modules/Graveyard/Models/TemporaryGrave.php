<?php

namespace Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Members\Models\Member;
use Modules\Graveyard\Models\GraveCategories;
use Carbon\Carbon;

class TemporaryGrave extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grave_id',
        'section',
        'row_no',
        'grave_no',
        'old_no',
        'status',
        'last_burial_date',
        'buried_name',
        'remarks',
        'plot_size',
        'member_id',
        'grave_category_id',
        'destination_permanent_grave_id',
        'contact_no',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_burial_date' => \App\Casts\DateString::class,
        'plot_size' => 'decimal:2',
        'is_active' => 'boolean',
        'row_no' => 'integer',
        'grave_no' => 'integer',
    ];

    /**
     * Get the bookings for this temporary grave
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
     * Get the member associated with this grave
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * Get the grave category associated with this grave
     */
    public function graveCategory()
    {
        return $this->belongsTo(GraveCategories::class, 'grave_category_id');
    }

    /**
     * Get the destination permanent grave for bone transfer
     */
    public function destinationPermanentGrave()
    {
        return $this->belongsTo(PermanentGrave::class, 'destination_permanent_grave_id');
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
     * Scope to search graves
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('section', 'like', "%{$search}%")
                ->orWhere('grave_no', 'like', "%{$search}%")
                ->orWhere('old_no', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT(section, '-', row_no, '-', grave_no) LIKE ?", ["%{$search}%"])
                ->orWhereHas('member', function ($memberQuery) use ($search) {
                    $memberQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                        ->orWhere('family_no', 'like', "%{$search}%")
                        ->orWhere('contact_no_1', 'like', "%{$search}%")
                        ->orWhere('contact_no_2', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Scope to get graves that need transfer to niche
     */
    public function scopeNeedingTransfer($query)
    {
        $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
        return $query->where('status', 'unavailable')
            ->whereNotNull('last_burial_date')
            ->whereRaw('DATEDIFF(NOW(), last_burial_date) >= ? * 30', [$monthsFromEnv]);
    }

    /**
     * Scope to get graves that need transfer to permanent grave
     */
    public function scopeNeedingPermanentTransfer($query)
    {
        $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
        return $query->where('status', 'unavailable')
            ->whereNotNull('last_burial_date')
            ->whereNotNull('destination_permanent_grave_id')
            ->whereRaw('DATEDIFF(NOW(), last_burial_date) >= ? * 30', [$monthsFromEnv]);
    }

    /**
     * Scope to get graves that need transfer to niche (default)
     */
    public function scopeNeedingNicheTransfer($query)
    {
        $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
        return $query->where('status', 'unavailable')
            ->whereNotNull('last_burial_date')
            ->whereNull('destination_permanent_grave_id')
            ->whereRaw('DATEDIFF(NOW(), last_burial_date) >= ? * 30', [$monthsFromEnv]);
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
     * Check if the grave needs transfer to niche
     */
    public function needsTransfer()
    {
        if (!$this->last_burial_date || $this->status === 'available') {
            return false;
        }

        $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
        $transferDate = Carbon::parse($this->last_burial_date)->addMonths($monthsFromEnv);
        return now()->gte($transferDate);
    }

    /**
     * Get the transfer due date
     */
    public function getTransferDueDateAttribute()
    {
        if (!$this->last_burial_date) {
            return null;
        }

        $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
        return Carbon::parse($this->last_burial_date)->addMonths($monthsFromEnv);
    }

    /**
     * Get the transfer destination type
     */
    public function getTransferDestinationType()
    {
        return $this->destination_permanent_grave_id ? 'permanent' : 'niche';
    }

    /**
     * Check if bones should be transferred to permanent grave
     */
    public function shouldTransferToPermanentGrave()
    {
        return !is_null($this->destination_permanent_grave_id);
    }

    /**
     * Check if bones should be transferred to niche (default behavior)
     */
    public function shouldTransferToNiche()
    {
        return is_null($this->destination_permanent_grave_id);
    }

    /**
     * Get transfer destination description
     */
    public function getTransferDestinationDescription()
    {
        if ($this->shouldTransferToPermanentGrave()) {
            $permanentGrave = $this->destinationPermanentGrave;
            return "Permanent Grave: {$permanentGrave->full_identifier}";
        }
        
        return "Niche";
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
     * Release the grave (make it available again)
     */
    public function release()
    {
        $this->update([
            'status' => 'available',
            'last_burial_date' => null,
        ]);
    }
}
