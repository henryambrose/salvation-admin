<?php

namespace Modules\Members\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleAction extends Model
{
    protected $fillable = ['action', 'name', 'slug', 'module_id'];

    /**
     * Get the module that owns the action.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
