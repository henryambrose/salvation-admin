<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\CellsAndAssociationMember;
use Modules\Members\Models\User;

class CellsAndAssociationMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-member');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CellsAndAssociationMember $cellsAndAssociationMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-member');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('create-member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CellsAndAssociationMember $cellsAndAssociationMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('update-member');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CellsAndAssociationMember $cellsAndAssociationMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-member');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CellsAndAssociationMember $cellsAndAssociationMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('restore-member');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CellsAndAssociationMember $cellsAndAssociationMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-member');
    }
}
