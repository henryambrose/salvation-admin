<?php

namespace Modules\Members\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PPCHead extends Model
{
    /** @use HasFactory<\Database\Factories\PPCHeadFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'p_p_c_heads';

    protected $fillable = [
        'member_id',
        'community_id',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
