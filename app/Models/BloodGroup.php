<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodGroup extends Model
{
    /** @use HasFactory<\Database\Factories\BloodGroupFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;
}
