<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CellsAndAssociationLeader extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cells_and_association_leaders';

    protected $fillable = [
        'cells_and_association_id',
        'leader_member_id',
        'assistant_leader_member_id',
    ];

    public function cellsAndAssociation()
    {
        return $this->belongsTo(CellsAndAssociation::class);
    }

    public function leader()
    {
        return $this->belongsTo(Member::class, 'leader_member_id');
    }

    public function assistantLeader()
    {
        return $this->belongsTo(Member::class, 'assistant_leader_member_id');
    }
}
