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
        'baptism_date',
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
    ];

    protected $casts = [
        'baptism_date' => 'date',
    ];

    /**
     * Get the member this baptism record belongs to
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the parish where baptism occurred
     */
    public function baptismParish(): BelongsTo
    {
        return $this->belongsTo(Parish::class, 'baptism_parish_id');
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
