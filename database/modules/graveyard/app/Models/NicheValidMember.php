<?php

namespace Database\Modules\Graveyard\App\Models;

use App\Models\Member;
use Database\Modules\Graveyard\App\Models\NicheGrave;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NicheValidMember extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'niche_valid_member';

    protected $fillable = [
        'niche_grave_id',
        'first_name',
        'last_name',
        'member_id',
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
     * Get the niche grave associated with the valid member.
     */
    public function nicheGrave()
    {
        return $this->belongsTo(NicheGrave::class);
    }
}

