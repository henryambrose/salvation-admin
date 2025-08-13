<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnifiedPerson extends Model
{
    protected $table = 'unified_people';

    public $timestamps = false;

    protected $primaryKey = 'uid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uid',
        'first_name',
        'last_name',
        'gender_id',
        'father_uid',
        'mother_uid',
        'spouse_uid',
        'family_no',
        'source',
    ];

    // ─────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────

    public function getFatherAttribute(): ?UnifiedPerson
    {
        return $this->father_uid ? UnifiedPerson::find($this->father_uid) : null;
    }

    public function getMotherAttribute(): ?UnifiedPerson
    {
        return $this->mother_uid ? UnifiedPerson::find($this->mother_uid) : null;
    }

    public function getSpouseAttribute(): ?UnifiedPerson
    {
        return $this->spouse_uid ? UnifiedPerson::find($this->spouse_uid) : null;
    }

    // ─────────────────────────────────────────────────────────────
    // Family Tree Methods with `family_no` Filtering
    // ─────────────────────────────────────────────────────────────
    public function getSpouse()
    {
        return $this->spouse;
    }

    public function getChildren()
    {
        return UnifiedPerson::where('family_no', $this->family_no)
            ->where(function ($query) {
                $query->where('father_uid', $this->uid)
                    ->orWhere('mother_uid', $this->uid);
            })->get();
    }

    public function getSons()
    {
        return UnifiedPerson::where('family_no', $this->family_no)
            ->where(function ($query) {
                $query->where('father_uid', $this->uid)
                    ->orWhere('mother_uid', $this->uid);
            })
            ->where('gender_id', 1)
            ->get();
    }

    public function getDaughters()
    {
        return UnifiedPerson::where('family_no', $this->family_no)
            ->where(function ($query) {
                $query->where('father_uid', $this->uid)
                    ->orWhere('mother_uid', $this->uid);
            })
            ->where('gender_id', 2)
            ->get();
    }

    public function getSiblings()
    {
        return UnifiedPerson::where('family_no', $this->family_no)
            ->where(function ($query) {
                $query->where('father_uid', $this->father_uid)
                    ->orWhere('mother_uid', $this->mother_uid);
            })
            ->where('uid', '!=', $this->uid)
            ->get();
    }

    public function getBrothers()
    {
        return $this->getSiblings()->where('gender_id', 1);
    }

    public function getSisters()
    {
        return $this->getSiblings()->where('gender_id', 2);
    }

    // ─────────────────────────────────────────────────────────────
    // Extended Generation Methods
    // ─────────────────────────────────────────────────────────────
    
    public function getGrandchildren()
    {
        $grandchildren = collect();
        foreach ($this->getChildren() as $child) {
            $childChildren = $child->getChildren();
            if ($childChildren->isNotEmpty()) {
                $grandchildren = $grandchildren->merge($childChildren);
            }
        }
        return $grandchildren;
    }

    public function getGreatGrandchildren()
    {
        $greatGrandchildren = collect();
        foreach ($this->getGrandchildren() as $grandchild) {
            $grandchildChildren = $grandchild->getChildren();
            if ($grandchildChildren->isNotEmpty()) {
                $greatGrandchildren = $greatGrandchildren->merge($grandchildChildren);
            }
        }
        return $greatGrandchildren;
    }

    public function getGreatGreatGrandchildren()
    {
        $greatGreatGrandchildren = collect();
        foreach ($this->getGreatGrandchildren() as $greatGrandchild) {
            $greatGrandchildChildren = $greatGrandchild->getChildren();
            if ($greatGrandchildChildren->isNotEmpty()) {
                $greatGreatGrandchildren = $greatGreatGrandchildren->merge($greatGrandchildChildren);
            }
        }
        return $greatGreatGrandchildren;
    }

    // ─────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.($this->last_name ?? ''));
    }

    public function getGenderNameAttribute(): string
    {
        return $this->gender_id == 1 ? 'Male' :
               ($this->gender_id == 2 ? 'Female' : 'Other');
    }

    public function isMember(): bool
    {
        return $this->source === 'Member';
    }

    public function isExternal(): bool
    {
        return $this->source === 'External';
    }

    public function getOriginalId(): int
    {
        return (int) str_replace(['M-', 'E-'], '', $this->uid);
    }
}
