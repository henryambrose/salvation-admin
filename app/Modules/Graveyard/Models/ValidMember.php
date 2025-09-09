<?php

namespace Modules\Graveyard\Models;

use \Modules\Members\Models\Member;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Graveyard\Models\Niche;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValidMember extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'valid_members';

    protected $fillable = [
        'permanent_grave_id',
        'niche_id',
        'member_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'age',
        'months',
        'days',
        'gender_id',
        'nationality',
        'parish_id',
        'contact_no',
        'aadhar_no',
        'member_type', // 'member' or 'external'
        'grave_type', // 'permanent_grave' or 'niche'
        'relationship_id', // relationship to grave owner (foreign key)
        'death_date',
        'burial_date',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'death_date' => 'date',
        'burial_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the member associated with the valid member.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the permanent grave associated with the valid member.
     */
    public function permanentGrave()
    {
        return $this->belongsTo(PermanentGrave::class);
    }

    /**
     * Get the niche associated with the valid member.
     */
    public function niche()
    {
        return $this->belongsTo(Niche::class);
    }

    /**
     * Get the gender associated with the valid member.
     */
    public function gender()
    {
        return $this->belongsTo(\Modules\Members\Models\Gender::class);
    }

    /**
     * Get the parish associated with the valid member.
     */
    public function parish()
    {
        return $this->belongsTo(\Modules\Members\Models\Parish::class);
    }

    /**
     * Get the relationship associated with the valid member.
     */
    public function relationship()
    {
        return $this->belongsTo(\Modules\Members\Models\Relationship::class);
    }

    /**
     * Get the grave (either permanent grave or niche) associated with the valid member.
     */
    public function grave()
    {
        return $this->permanent_grave_id 
            ? $this->permanentGrave() 
            : $this->niche();
    }

    /**
     * Get the grave type.
     */
    public function getGraveTypeAttribute()
    {
        return $this->permanent_grave_id ? 'permanent_grave' : 'niche';
    }

    /**
     * Get the grave instance.
     */
    public function getGraveInstanceAttribute()
    {
        return $this->permanent_grave_id 
            ? $this->permanentGrave 
            : $this->niche;
    }

    /**
     * Get the full name of the valid member.
     */
    public function getFullNameAttribute()
    {
        if ($this->member_id && $this->member) {
            return $this->member->first_name . ' ' . $this->member->last_name;
        }
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if this is a parish member.
     */
    public function getIsParishMemberAttribute()
    {
        return !is_null($this->member_id);
    }

    /**
     * Check if this member is deceased.
     */
    public function getIsDeceasedAttribute()
    {
        return !is_null($this->death_date);
    }

    /**
     * Get the creator user.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the updater user.
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
