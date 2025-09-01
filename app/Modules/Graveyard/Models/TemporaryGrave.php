<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Members\Models\Member;
use Carbon\Carbon;

class TemporaryGrave extends Model
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
        'duration_months',
        'remarks',
        'plot_size',
        'owner_name',
        'member_id',
        'contact_no',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_burial_date' => 'date',
        'plot_size' => 'decimal:2',
        'is_active' => 'boolean',
        'row_no' => 'integer',
        'grave_no' => 'integer',
        'duration_months' => 'integer',
    ];

    /**
     * Get the bookings for this temporary grave
     */
    public function bookings()
    {
        return $this->hasMany(GraveBooking::class, 'temporary_grave_id');
    }

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
     * Get the latest booking for this grave
     */
    public function latestBooking()
    {
        return $this->hasOne(GraveBooking::class, 'temporary_grave_id')->latest();
    }

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
              ->orWhere('oldno', 'like', "%{$search}%")
              ->orWhere('owner_name', 'like', "%{$search}%")
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
        return $query->where('status', 'unavailable')
                    ->whereNotNull('last_burial_date')
                    ->whereRaw('DATEDIFF(NOW(), last_burial_date) >= duration_months * 30');
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

        $transferDate = Carbon::parse($this->last_burial_date)->addMonths($this->duration_months);
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

        return Carbon::parse($this->last_burial_date)->addMonths($this->duration_months);
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
