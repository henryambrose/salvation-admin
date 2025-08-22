<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'family_no',
        'community_id',
        'father_id',
        'mother_id',
        'spouse_id',
        'father_source',
        'mother_source',
        'spouse_source',
        'relationship_id',
        'gender_id',
        'external_member_no',
        'uid',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($externalMember) {
            if (! $externalMember->external_member_no) {
                $currentYear = date('Y');
                $lastMember = static::where('external_member_no', 'like', $currentYear.'-EXT-%')
                    ->orderBy('external_member_no', 'desc')
                    ->first();

                if ($lastMember) {
                    $lastNumber = (int) substr($lastMember->external_member_no, -6);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $externalMember->external_member_no = $currentYear.'-EXT-'.str_pad($newNumber, 6, '0', STR_PAD_LEFT);
            }
        });

    }

    protected $casts = [
        'father_id' => 'integer',
        'mother_id' => 'integer',
        'spouse_id' => 'integer',
        'relationship_id' => 'integer',
        'gender_id' => 'integer',
        'community_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

   
    /**
     * Get the full name of the external member
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->last_name ?? ''));
    }

    /**
     * Get the relationship type
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
    }

    /**
     * Get the gender
     */
    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    /**
     * Get the father (internal member)
     */
    public function fatherInternal(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'father_id');
    }

    /**
     * Get the mother (internal member)
     */
    public function motherInternal(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'mother_id');
    }

    /**
     * Get the spouse (internal member)
     */
    public function spouseInternal(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'spouse_id');
    }

    /**
     * Get the father (external member)
     */
    public function fatherExternal(): BelongsTo
    {
        return $this->belongsTo(ExternalMember::class, 'father_id');
    }

    /**
     * Get the mother (external member)
     */
    public function motherExternal(): BelongsTo
    {
        return $this->belongsTo(ExternalMember::class, 'mother_id');
    }

    /**
     * Get the spouse (external member)
     */
    public function spouseExternal(): BelongsTo
    {
        return $this->belongsTo(ExternalMember::class, 'spouse_id');
    }


    /**
     * Get children (external members who have this member as parent)
     */
    public function children()
    {
        return $this->hasMany(ExternalMember::class, 'father_id')
            ->orWhere('mother_id', $this->id);
    }

    /**
     * Get siblings (external members with same parents)
     */
    public function siblings()
    {
        $parentIds = collect([$this->father_id, $this->mother_id])->filter();

        if ($parentIds->isEmpty()) {
            return collect();
        }

        return ExternalMember::where(function ($query) use ($parentIds) {
            $query->whereIn('father_id', $parentIds)
                ->orWhereIn('mother_id', $parentIds);
        })
            ->where('id', '!=', $this->id)
            ->get();
    }

    /**
     * Check if this external member has a parent in the members table
     */
    public function hasInternalParent(): bool
    {
        return Member::whereIn('id', [$this->father_id, $this->mother_id])->exists();
    }

    /**
     * Check if this external member has a parent in the external_members table
     */
    public function hasExternalParent(): bool
    {
        return ExternalMember::whereIn('id', [$this->father_id, $this->mother_id])->exists();
    }

    /**
     * Get the type of member (internal or external)
     */
    public function getMemberTypeAttribute(): string
    {
        return 'external';
    }

    public function getUidAttribute(): string
    {
        return 'E-'.$this->id;
    }
}
