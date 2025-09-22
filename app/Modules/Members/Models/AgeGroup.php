<?php

namespace Modules\Members\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgeGroup extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'description',
        'min_age',
        'max_age',
    ];

    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
    ];
}
