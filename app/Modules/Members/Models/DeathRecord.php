<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeathRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'death_archive_certificate_id',
        'member_id',
        'death_date',
        'burial_date',
        'burial_reg_no',
        'burial_parish_id',
        'deceased_name',
        'deceased_surname',
        'relationship',
        'residence',
        'age',
        'nationality',
        'cause_of_death',
        'place_of_burial',
        'minister_name',
        'death_remarks',
    ];

    protected $casts = [
        'death_date' => 'date:Y-m-d',
        'burial_date' => 'date:Y-m-d',
        'age' => 'integer',
    ];

    /**
     * Get the member this death record belongs to
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the member who has this as their death record (reverse relationship)
     */
    public function memberReference()
    {
        return $this->hasOne(Member::class, 'deathrecord_id');
    }

    /**
     * Get the parish where burial occurred
     */
    public function burialParish(): BelongsTo
    {
        return $this->belongsTo(Parish::class, 'burial_parish_id');
    }

    /**
     * Get formatted death date
     */
    public function getFormattedDeathDateAttribute(): ?string
    {
        return $this->death_date?->format('d F Y');
    }

    /**
     * Get formatted burial date
     */
    public function getFormattedBurialDateAttribute(): ?string
    {
        return $this->burial_date?->format('d F Y');
    }

    /**
     * Get burial year for certificate numbering
     */
    public function getBurialYearAttribute(): ?int
    {
        return $this->burial_date?->year;
    }

    /**
     * Get the death archive certificate this record was created from
     */
    public function deathArchiveCertificate(): BelongsTo
    {
        return $this->belongsTo(DeathArchiveCertificate::class, 'death_archive_certificate_id');
    }
}
