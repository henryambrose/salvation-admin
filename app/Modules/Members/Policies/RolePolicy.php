<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('read-role') || $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('read-role') || $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-role') || $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // Prevent editing system roles unless super admin
        if (in_array($role->name, ['super admin', 'admin', 'viewer'])) {
            return $user->hasRole('super admin');
        }
        
        return $user->can('update-role') || $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Prevent deleting system roles unless super admin
        if (in_array($role->name, ['super admin', 'admin', 'viewer'])) {
            return $user->hasRole('super admin');
        }
        
        return $user->can('delete-role') || $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->can('restore-role') || $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        // Prevent force deleting system roles
        if (in_array($role->name, ['super admin', 'admin', 'viewer'])) {
            return false;
        }
        
        return $user->can('delete-role') || $user->hasRole('super admin');
    }
}
