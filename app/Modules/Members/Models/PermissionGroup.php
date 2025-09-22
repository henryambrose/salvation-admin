<?php

namespace Modules\Members\Models;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the permissions that belong to this group.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(ModuleAction::class, 'permission_group_permissions', 'permission_group_id', 'module_action_id');
    }

    /**
     * Get the permission slugs for this group.
     */
    public function getPermissionSlugs(): array
    {
        return $this->permissions()->pluck('slug')->toArray();
    }

    /**
     * Check if this group has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }
}
