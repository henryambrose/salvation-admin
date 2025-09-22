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
    ];

    protected $casts = [
        'last_burial_date' => 'date',
        'plot_size' => 'decimal:2',
        'is_active' => 'boolean',
        'row_no' => 'integer',
        'grave_no' => 'integer',
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
}
