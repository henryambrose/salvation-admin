<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SCCHead extends Model
{
    /** @use HasFactory<\Database\Factories\SCCHeadFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 's_c_c_heads'; // <-- Add this line

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
