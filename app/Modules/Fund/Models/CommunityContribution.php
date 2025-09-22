<?php

namespace Modules\Fund\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\User;

class CommunityContribution extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'contribution_type_id',
        'collection_date',
        'amount',
        'description',
        'location',
        'collected_by_user_id',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'collection_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Available status options
     */
    const STATUS_RECORDED = 'recorded';
    const STATUS_VERIFIED = 'verified';
    const STATUS_ARCHIVED = 'archived';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_RECORDED => 'Recorded',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Get the contribution type for this contribution
     */
    public function contributionType()
    {
        return $this->belongsTo(CommunityContributionType::class, 'contribution_type_id');
    }

    /**
     * Get the user who collected this contribution
     */
    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by_user_id');
    }

    /**
     * Get the creator user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater user
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by contribution type
     */
    public function scopeByType($query, $typeId)
    {
        return $query->where('contribution_type_id', $typeId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('collection_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by current year
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('collection_date', now()->year);
    }

    /**
     * Scope to filter by specific year
     */
    public function scopeForYear($query, $year)
    {
        return $query->whereYear('collection_date', $year);
    }

    /**
     * Scope to exclude archived contributions
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_ARCHIVED);
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }

    /**
     * Get formatted collection date
     */
    public function getFormattedCollectionDateAttribute()
    {
        return $this->collection_date->format('d M Y');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_RECORDED => 'yellow',
            self::STATUS_VERIFIED => 'green',
            self::STATUS_ARCHIVED => 'gray',
            default => 'gray'
        };
    }

    /**
     * Check if contribution can be edited
     */
    public function isEditable()
    {
        return $this->status !== self::STATUS_ARCHIVED;
    }

    /**
     * Check if contribution can be verified
     */
    public function canBeVerified()
    {
        return $this->status === self::STATUS_RECORDED;
    }

    /**
     * Check if contribution can be archived
     */
    public function canBeArchived()
    {
        return $this->status === self::STATUS_VERIFIED;
    }
}