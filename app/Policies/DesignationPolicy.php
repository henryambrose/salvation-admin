<?php

namespace App\Policies;

use App\Models\Designation;
use App\Models\User;

class DesignationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-designation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Designation $designation): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-designation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('create-designation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Designation $designation): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('update-designation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Designation $designation): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-designation');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Designation $designation): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('restore-designation');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Designation $designation): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-designation');
    }
}
