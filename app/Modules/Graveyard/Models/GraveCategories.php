<?php

namespace  Modules\Graveyard\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GraveCategories extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
    ];

    public function temporaryGraves()
    {
        return $this->hasMany(\Modules\Graveyard\Models\TemporaryGrave::class, 'grave_category_id');
    }

    public static function getForDropdown()
    {
        return self::orderBy('name')->pluck('name', 'id');
    }
}
