<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExternalMember extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'family_no',
        'relationship_id'
    ];

    /**
     * Get the relationship type
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
    }

    /**
     * Get the family links where this external member is the primary member
     */
    public function familyLinks(): HasMany
    {
        return $this->hasMany(FamilyLink::class, 'external_member_id');
    }

    /**
     * Get the family links where this external member is the related member
     */
    public function relatedFamilyLinks(): HasMany
    {
        return $this->hasMany(FamilyLink::class, 'related_external_member_id');
    }
}
