<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyIncomeRange extends Model
{
    /** @use HasFactory<\Database\Factories\FamilyIncomeRangeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'starting_range',
        'ending_range',
    ];

    public $timestamps = false;

}
