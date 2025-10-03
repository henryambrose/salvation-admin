<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\DB;

class CertificateRecord extends Model
{
  use HasFactory, SoftDeletes, Authorizable;

  protected $fillable = [
    'member_id',
    'certificate_type_id',
    'template_id',
    'issued_date',
    'issued_by',
    'certificate_number',
    'additional_data',
    'file_path',
    'file_hash',
    'download_count',
    'last_downloaded_at',
    'notes',
    'is_reprint',
    'original_certificate_id',
  ];

  protected $casts = [
    'additional_data' => 'array',
    'issued_date' => 'date',
    'last_downloaded_at' => 'datetime',
    'is_reprint' => 'boolean',
  ];

  protected $appends = [
    'certificate_type',
    'certificate_type_name',
    'formatted_type',
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
   * Get the member this certificate belongs to
   */
  public function member(): BelongsTo
  {
    return $this->belongsTo(Member::class);
  }

  /**
   * Get the template used for this certificate
   */
  public function template(): BelongsTo
  {
    return $this->belongsTo(CertificateTemplate::class);
  }

  /**
   * Get the user who issued this certificate
   */
  public function issuer(): BelongsTo
  {
    return $this->belongsTo(User::class, 'issued_by');
  }

  /**
   * Get the certificate type this certificate belongs to
   */
  public function certificateType(): BelongsTo
  {
    return $this->belongsTo(\Modules\Members\Models\CertificateType::class);
  }

  /**
   * Get the original certificate if this is a reprint
   */
  public function originalCertificate(): BelongsTo
  {
    return $this->belongsTo(self::class, 'original_certificate_id');
  }

  /**
   * Scope to get certificates by type (by code)
   */
  public function scopeByType($query, string $type)
  {
    // Get the certificate type ID first to avoid relationship queries
    $certificateType = \Modules\Members\Models\CertificateType::where('code', $type)->first();
    if ($certificateType) {
      return $query->where('certificate_type_id', $certificateType->id);
    }

    // Return empty result if certificate type not found
    return $query->whereRaw('1 = 0');
  }

  /**
   * Scope to get certificates by member
   */
  public function scopeByMember($query, int $memberId)
  {
    return $query->where('member_id', $memberId);
  }

  /**
   * Scope to get certificates issued in date range
   */
  public function scopeIssuedBetween($query, $startDate, $endDate)
  {
    return $query->whereBetween('issued_date', [$startDate, $endDate]);
  }

  /**
   * Scope to exclude reprints
   */
  public function scopeOriginalOnly($query)
  {
    return $query->where('is_reprint', false);
  }

  /**
   * Generate unique certificate number with better concurrency handling
   */
  public static function generateCertificateNumber(string $type, int $memberId): string
  {
    $year = now()->year;
    $typeCode = strtoupper(substr($type, 0, 3)); // BAP, CON, MAR, etc.
    $memberCode = str_pad($memberId, 6, '0', STR_PAD_LEFT);

    // Get the certificate type ID first
    $certificateType = \Modules\Members\Models\CertificateType::where('code', $type)->first();
    if (!$certificateType) {
      throw new \Exception("Certificate type '{$type}' not found");
    }

    return DB::transaction(function () use ($typeCode, $year, $memberCode, $certificateType) {
      // Get the highest sequence number for this type and year with lock
      $lastCertificate = static::where('certificate_number', 'LIKE', "{$typeCode}/{$year}/%")
        ->orderBy('certificate_number', 'desc')
        ->lockForUpdate()
        ->first();

      $sequence = 1;
      if ($lastCertificate) {
        // Extract sequence number from the last certificate
        $parts = explode('/', $lastCertificate->certificate_number);
        if (count($parts) === 4) {
          $lastSequence = (int) end($parts);
          $sequence = $lastSequence + 1;
        }
      }

      // Generate certificate number with retry logic for edge cases
      $maxAttempts = 50;
      for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
        $sequenceStr = str_pad($sequence + $attempt, 4, '0', STR_PAD_LEFT);
        $certificateNumber = "{$typeCode}/{$year}/{$memberCode}/{$sequenceStr}";

        // Double-check if this number is already taken with lock
        $exists = static::where('certificate_number', $certificateNumber)
          ->lockForUpdate()
          ->exists();
        if (!$exists) {
          return $certificateNumber;
        }
      }

      // Fallback to timestamp with microseconds if we can't find a unique sequence
      $timestamp = now()->format('His') . substr(microtime(), 2, 6);
      return "{$typeCode}/{$year}/{$memberCode}/{$timestamp}";
    });
  }

  /**
   * Get formatted type name
   */
  public function getFormattedTypeAttribute(): string
  {
    $certificateType = \Modules\Members\Models\CertificateType::find($this->certificate_type_id);
    return $certificateType ? $certificateType->name : 'Unknown Certificate Type';
  }

  /**
   * Get certificate type name for display
   */
  public function getCertificateTypeNameAttribute(): string
  {
    $certificateType = \Modules\Members\Models\CertificateType::find($this->certificate_type_id);
    return $certificateType ? $certificateType->name : 'Unknown Certificate Type';
  }

  /**
   * Get certificate type code (for backward compatibility)
   */
  public function getCertificateTypeAttribute(): ?string
  {
    // Temporarily load directly to avoid relationship issues
    $certificateType = \Modules\Members\Models\CertificateType::find($this->certificate_type_id);
    return $certificateType?->code;
  }

  /**
   * Get file URL if file exists
   */
  public function getFileUrlAttribute(): ?string
  {
    if ($this->file_path && Storage::disk('local')->exists($this->file_path)) {
      return route('certificates.download', $this->id);
    }
    return null;
  }

  /**
   * Check if file exists
   */
  public function fileExists(): bool
  {
    return $this->file_path && Storage::disk('local')->exists($this->file_path);
  }

  /**
   * Increment download count
   */
  public function incrementDownloadCount(): void
  {
    $this->increment('download_count');
    $this->update(['last_downloaded_at' => now()]);
  }

  /**
   * Get the required additional data fields based on certificate type
   */
  public static function getRequiredAdditionalData(string $type): array
  {
    return match ($type) {
      'baptism' => [
        'godfather_name' => 'Godfather Name',
        'godmother_name' => 'Godmother Name',
        'priest_name' => 'Priest Name',
        'witnesses' => 'Witnesses',
      ],
      'confirmation' => [
        'confirmation_name' => 'Confirmation Name',
        'sponsor_name' => 'Sponsor Name',
        'bishop_name' => 'Bishop Name',
        'priest_name' => 'Priest Name',
      ],
      'marriage' => [
        'spouse_name' => 'Spouse Name',
        'witness1_name' => 'Witness 1 Name',
        'witness2_name' => 'Witness 2 Name',
        'priest_name' => 'Priest Name',
        'marriage_type' => 'Marriage Type',
      ],
      'membership' => [
        'join_date' => 'Join Date',
        'community_name' => 'Community Name',
        'family_number' => 'Family Number',
      ],
      'death' => [
        'burial_date' => 'Burial Date',
        'burial_place' => 'Burial Place',
        'priest_name' => 'Priest Name',
        'last_rites_given' => 'Last Rites Given',
      ],
      default => [],
    };
  }
}
