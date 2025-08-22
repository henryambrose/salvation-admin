<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityCluster extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'community_id',
        'cluster_id',
        'member_id',
    ];

    public function cluster()
    {
        return $this->belongsTo(Cluster::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'community_cluster_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
