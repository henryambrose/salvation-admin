<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CellsAndAssociationMember extends Model
{
    /** @use HasFactory<\Database\Factories\CellsAndAssociationMemberFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['member_id', 'cells_and_association_id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function cellsAndAssociation()
    {
        return $this->belongsTo(CellsAndAssociation::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->diffForHumans();
    }
}
