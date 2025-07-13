<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    /**
     * Get the actions associated with the module.
     */
    public function actions()
    {
        return $this->hasMany(ModuleAction::class);
    }
}
