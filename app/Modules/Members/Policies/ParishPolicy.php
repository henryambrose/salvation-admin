<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\Parish;
use Modules\Members\Models\User;

class ParishPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-parish');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Parish $parish): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-parish');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('create-parish');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Parish $parish): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('update-parish');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Parish $parish): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-parish');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Parish $parish): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('restore-parish');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Parish $parish): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-parish');
    }
}
