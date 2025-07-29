<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cluster extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function communityClusters()
    {
        return $this->hasMany(CommunityCluster::class);
    }

    public function communityClusterHeads()
    {
        return $this->hasManyThrough(CommunityClusterHead::class, CommunityCluster::class);
    }
}
