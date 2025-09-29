<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateTemplate extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'name',
    'type',
    'template_content',
    'template_config',
    'language',
    'is_active',
    'is_default',
    'description',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'template_config' => 'array',
    'is_active' => 'boolean',
    'is_default' => 'boolean',
    'additional_data' => 'array',
  ];

  /**
   * Certificate types enum
   */
  public const TYPES = [
    'baptism' => 'Baptism Certificate',
    'confirmation' => 'Confirmation Certificate',
    'marriage' => 'Marriage Certificate',
    'membership' => 'Membership Certificate',
    'death' => 'Death Certificate',
  ];

  /**
   * Get the user who created this template
   */
  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  /**
   * Get the user who last updated this template
   */
  public function updater(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  /**
   * Get certificate records using this template
   */
  public function certificateRecords(): HasMany
  {
    return $this->hasMany(CertificateRecord::class, 'template_id');
  }

  /**
   * Scope to get active templates
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  /**
   * Scope to get templates by type
   */
  public function scopeByType($query, string $type)
  {
    return $query->where('type', $type);
  }

  /**
   * Scope to get default template for a type
   */
  public function scopeDefault($query)
  {
    return $query->where('is_default', true);
  }

  /**
   * Get the default template for a specific type
   */
  public static function getDefaultForType(string $type): ?self
  {
    return static::active()
      ->byType($type)
      ->default()
      ->first();
  }

  /**
   * Get formatted type name
   */
  public function getFormattedTypeAttribute(): string
  {
    return self::TYPES[$this->type] ?? ucfirst($this->type);
  }

  /**
   * Check if template can be deleted
   */
  public function canBeDeleted(): bool
  {
    // Cannot delete if it has been used for certificates
    return $this->certificateRecords()->count() === 0;
  }
}
