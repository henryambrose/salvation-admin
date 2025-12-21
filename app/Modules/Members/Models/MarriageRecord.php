<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarriageRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'marriage_date',
        'marriage_reg_no',
        'parish_of_marriage',
        'bridegroom_member_id',
        'bridegroom_name',
        'bridegroom_surname',
        'bridegroom_dob',
        'bridegroom_nationality',
        'bridegroom_profession',
        'bridegroom_residence',
        'bridegroom_father_name',
        'bridegroom_mother_name',
        'bridegroom_status',
        'bridegroom_if_widower_whose',
        'bride_member_id',
        'bride_name',
        'bride_surname',
        'bride_dob',
        'bride_nationality',
        'bride_profession',
        'bride_residence',
        'bride_father_name',
        'bride_mother_name',
        'bride_status',
        'bride_if_widow_whose',
        'first_witness_name',
        'first_witness_residence',
        'second_witness_name',
        'second_witness_residence',
        'minister_name',
        'marriage_remarks',
    ];

    protected $casts = [
        'marriage_date' => 'date:Y-m-d',
        'bridegroom_dob' => 'date:Y-m-d',
        'bride_dob' => 'date:Y-m-d',
    ];

    /**
     * Get the bridegroom member
     */
    public function bridegroom(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'bridegroom_member_id');
    }

    /**
     * Get the bride member
     */
    public function bride(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'bride_member_id');
    }

    /**
     * Get the member who has this as their marriage record (reverse relationship)
     */
    public function memberReference()
    {
        return $this->hasOne(Member::class, 'marriagerecord_id');
    }

    /**
     * Get the parish where marriage occurred
     */
    public function marriageParish(): BelongsTo
    {
        return $this->belongsTo(Parish::class, 'marriage_parish_id');
    }

    /**
     * Get formatted marriage date
     */
    public function getFormattedMarriageDateAttribute(): ?string
    {
        return $this->marriage_date?->format('d F Y');
    }

    /**
     * Get marriage year for certificate numbering
     */
    public function getMarriageYearAttribute(): ?int
    {
        return $this->marriage_date?->year;
    }
}
