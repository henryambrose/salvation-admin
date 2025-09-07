<?php

namespace Modules\Graveyard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class ServiceType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'cost',
        'type',
        'category',
        'applicable_to',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the booking services for this service type
     */
    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get services by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get services by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Scope to get grave-related services
     */
    public function scopeGraveServices($query)
    {
        return $query->where('category', 'grave');
    }

    /**
     * Scope to get additional services (not grave-related)
     */
    public function scopeAdditionalServices($query)
    {
        return $query->whereIn('category', ['funeral', 'additional']);
    }

    /**
     * Check if this is a concession service
     */
    public function isConcession()
    {
        return $this->type === 'concession';
    }

    /**
     * Check if this is a free service
     */
    public function isFree()
    {
        return $this->type === 'free';
    }

    /**
     * Check if this is a normal paid service
     */
    public function isNormal()
    {
        return $this->type === 'normal';
    }

    /**
     * Get formatted cost
     */
    public function getFormattedCostAttribute()
    {
        return '₹' . number_format($this->cost, 2);
    }
}
