<?php

namespace Modules\Members\Models;

use App\Traits\Auditable;

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
