<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgeGroup extends Model
{
  use SoftDeletes;

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
