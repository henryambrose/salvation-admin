<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Community extends Model
{
    /** @use HasFactory<\Database\Factories\CommunityFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'zone_id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->diffForHumans();
    }

    public function communityClusters()
    {
        return $this->hasMany(CommunityCluster::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function ppchead()
    {
        return $this->hasOne(PPCHead::class);
    }

    public function scchead()
    {
        return $this->hasOne(SCCHead::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
