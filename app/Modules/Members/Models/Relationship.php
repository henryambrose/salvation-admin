<?php

namespace Modules\Members\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relationship extends Model
{
    /** @use HasFactory<\Database\Factories\RelationshipFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\RelationshipFactory::new();
    }
}
