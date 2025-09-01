<?php

namespace Modules\Graveyard\Models;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermanentValidMember extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'permanent_valid_member';

    protected $fillable = [
        'permanent_grave_id',
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
     * Get the permanent grave associated with the valid member.
     */
    public function permanentGrave()
    {
        return $this->belongsTo(PermanentGrave::class);
    }
}
