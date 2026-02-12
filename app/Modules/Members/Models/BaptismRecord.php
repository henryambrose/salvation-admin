<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaptismRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'birth_archive_certificate_id',
        'baptized_name',
        'baptized_surname',
        'baptism_date',
        'confirmation_date',
        'baptism_reg_no',
        'place_of_baptism',
        'baptism_parish_id',
        'place_of_birth',
        'nationality',
        'father_name',
        'father_residence',
        'father_profession',
        'mother_name',
        'godfather_name',
        'godfather_residence',
        'godmother_name',
        'godmother_residence',
        'minister_name',
        'baptism_remarks',
        'birth_date_text',
        'baptism_reg_year',
        'confirmation',
    ];

    protected $casts = [
        'baptism_date' => 'date',
        'confirmation_date' => 'date',
    ];

    /**
     * Get the member this baptism record belongs to
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the member who has this as their baptism record (reverse relationship)
     */
    public function memberReference()
    {
        return $this->hasOne(Member::class, 'baptismrecord_id');
    }

    /**
     * Get the parish where baptism occurred
     */
    public function baptismParish(): BelongsTo
    {
        return $this->belongsTo(Parish::class, 'baptism_parish_id');
    }

    /**
     * Get the birth archive certificate this baptism record was created from
     */
    public function birthArchiveCertificate(): BelongsTo
    {
        return $this->belongsTo(BirthArchiveCertificate::class, 'birth_archive_certificate_id');
    }

    /**
     * Get formatted baptism date
     */
    public function getFormattedBaptismDateAttribute(): ?string
    {
        return $this->baptism_date?->format('d F Y');
    }

    /**
     * Get baptism year for certificate numbering
     */
    public function getBaptismYearAttribute(): ?int
    {
        return $this->baptism_date?->year;
    }
}
