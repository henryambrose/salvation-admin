<?php

namespace Modules\Fund\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FundCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function familyContributions()
    {
        return $this->hasMany(FamilyContribution::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\Modules\Members\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\Modules\Members\Models\User::class, 'updated_by');
    }
}
