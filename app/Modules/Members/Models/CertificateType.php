<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;

class CertificateType extends Model
{
    use HasFactory, SoftDeletes, Authorizable;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'sort_order',
        'config',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the user who created this certificate type
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this certificate type
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get certificate templates for this type
     */
    public function certificateTemplates(): HasMany
    {
        return $this->hasMany(CertificateTemplate::class);
    }

    /**
     * Scope to get active certificate types
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
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope to get certificate type by code
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Get the default active template for this certificate type
     */
    public function getDefaultTemplate(): ?CertificateTemplate
    {
        return $this->certificateTemplates()
            ->active()
            ->where('is_default', true)
            ->first();
    }

    /**
     * Check if certificate type can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->certificateTemplates()->count() === 0;
    }
}