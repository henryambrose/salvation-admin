<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'app', 'color', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the rules for this category.
     */
    public function rules(): HasMany
    {
        return $this->hasMany(PermissionCategoryRule::class)->orderBy('priority', 'desc');
    }

    /**
     * Get all permissions that match this category's rules.
     */
    public function getMatchingPermissions($permissions)
    {
        $matching = collect();
        
        foreach ($permissions as $permission) {
            if ($this->matchesPermission($permission->name)) {
                $matching->push($permission);
            }
        }
        
        return $matching;
    }

    /**
     * Check if a permission matches this category's rules.
     */
    public function matchesPermission($permissionName): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->is_active) continue;
            
            switch ($rule->rule_type) {
                case 'contains':
                    if (str_contains(strtolower($permissionName), strtolower($rule->rule_value))) {
                        return true;
                    }
                    break;
                    
                case 'starts_with':
                    if (str_starts_with(strtolower($permissionName), strtolower($rule->rule_value))) {
                        return true;
                    }
                    break;
                    
                case 'ends_with':
                    if (str_ends_with(strtolower($permissionName), strtolower($rule->rule_value))) {
                        return true;
                    }
                    break;
                    
                case 'regex':
                    if (preg_match($rule->rule_value, $permissionName)) {
                        return true;
                    }
                    break;
            }
        }
        
        return false;
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}


