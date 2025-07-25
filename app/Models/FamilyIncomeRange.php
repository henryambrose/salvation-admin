<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyIncomeRange extends Model
{
    /** @use HasFactory<\Database\Factories\FamilyIncomeRangeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        // 'starting_range',
        // 'ending_range',
    ];

    public $timestamps = false;

}
