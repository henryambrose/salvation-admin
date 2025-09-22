<?php

namespace Modules\Members\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeRange extends Model
{
    /** @use HasFactory<\Database\Factories\IncomeRangeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'starting_range',
        'ending_range',
    ];

    public $timestamps = false;
}
