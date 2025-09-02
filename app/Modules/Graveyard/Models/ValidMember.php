<?php

namespace Modules\Graveyard\Models;

use App\Models\Member;
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
        'contact_no',
        'aadhar_no',
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
}
