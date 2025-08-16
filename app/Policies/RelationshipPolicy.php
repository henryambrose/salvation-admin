<?php

namespace App\Policies;

use App\Models\Relationship;
use App\Models\User;

class RelationshipPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-relationship');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Relationship $relationship): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-relationship');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('create-relationship');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Relationship $relationship): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('update-relationship');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Relationship $relationship): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-relationship');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Relationship $relationship): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('restore-relationship');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Relationship $relationship): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-relationship');
    }
}
