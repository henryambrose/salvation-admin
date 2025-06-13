<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    /** @use HasFactory<\Database\Factories\CommunityFactory> */
    use HasFactory;

    public function communityClusters()
    {
        return $this->hasMany(CommunityCluster::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
