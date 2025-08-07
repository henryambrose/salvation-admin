<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyLink extends Model
{
    protected $table = 'familylinks';
    
    protected $fillable = [
        'member_id',
        'external_member_id',
        'related_member_id',
        'related_external_member_id',
        'relationship_id'
    ];

    /**
     * Get the member who owns this relationship
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    /**
     * Get the external member who owns this relationship
     */
    public function externalMember(): BelongsTo
    {
        return $this->belongsTo(ExternalMember::class, 'external_member_id');
    }

    /**
     * Get the related member
     */
    public function relatedMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'related_member_id');
    }

    /**
     * Get the related external member
     */
    public function relatedExternalMember(): BelongsTo
    {
        return $this->belongsTo(ExternalMember::class, 'related_external_member_id');
    }

    /**
     * Get the relationship type
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
    }

    /**
     * Get the primary member (either regular or external)
     */
    public function getPrimaryMemberAttribute()
    {
        return $this->member ?? $this->externalMember;
    }

    /**
     * Get the related member (either regular or external)
     */
    public function getRelatedMemberAttribute()
    {
        return $this->relatedMember ?? $this->relatedExternalMember;
    }

    /**
     * Check if this link involves external members
     */
    public function hasExternalMembers(): bool
    {
        return !is_null($this->external_member_id) || !is_null($this->related_external_member_id);
    }

    /**
     * Get the reverse relationship
     */
    public function reverseRelationship()
    {
        return static::where(function ($query) {
            $query->where('member_id', $this->related_member_id)
                  ->where('related_member_id', $this->member_id);
        })->orWhere(function ($query) {
            $query->where('external_member_id', $this->related_external_member_id)
                  ->where('related_external_member_id', $this->external_member_id);
        })->orWhere(function ($query) {
            $query->where('member_id', $this->related_member_id)
                  ->where('related_external_member_id', $this->external_member_id);
        })->orWhere(function ($query) {
            $query->where('external_member_id', $this->related_external_member_id)
                  ->where('related_member_id', $this->member_id);
        })->first();
    }
}
