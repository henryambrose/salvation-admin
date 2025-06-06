<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SCCHead extends Model
{
    /** @use HasFactory<\Database\Factories\SCCHeadFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id',
        'community_id',
    ];
}
