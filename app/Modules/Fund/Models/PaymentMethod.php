<?php

namespace Modules\Fund\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
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

    /**
     * Scope to get only active payment methods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
