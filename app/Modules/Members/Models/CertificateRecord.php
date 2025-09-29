<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CertificateRecord extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'member_id',
    'certificate_type',
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
   * Get the original certificate if this is a reprint
   */
  public function originalCertificate(): BelongsTo
  {
    return $this->belongsTo(self::class, 'original_certificate_id');
  }

  /**
   * Scope to get certificates by type
   */
  public function scopeByType($query, string $type)
  {
    return $query->where('certificate_type', $type);
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
   * Generate unique certificate number
   */
  public static function generateCertificateNumber(string $type, int $memberId): string
  {
    $year = now()->year;
    $typeCode = strtoupper(substr($type, 0, 3)); // BAP, CON, MAR, etc.
    $memberCode = str_pad($memberId, 6, '0', STR_PAD_LEFT);

    // Count certificates of this type for this year
    $count = static::where('certificate_type', $type)
      ->whereYear('issued_date', $year)
      ->count() + 1;

    $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);

    return "{$typeCode}/{$year}/{$memberCode}/{$sequence}";
  }

  /**
   * Get formatted type name
   */
  public function getFormattedTypeAttribute(): string
  {
    return self::TYPES[$this->certificate_type] ?? ucfirst($this->certificate_type);
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
