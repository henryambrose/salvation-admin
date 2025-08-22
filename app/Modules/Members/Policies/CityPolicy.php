<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\City;
use Modules\Members\Models\User;

class CityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-city');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, City $city): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-city');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('create-city');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, City $city): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('update-city');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, City $city): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-city');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, City $city): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('restore-city');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, City $city): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-city');
    }
}
