<?php

namespace App\Policies;

use App\Models\ExternalMember;
use App\Models\User;

class ExternalMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }
        
        return $user->can('list-external-member');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExternalMember $externalMember): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }
        
        if (!$user->can('read-external-member')) {
            return false;
        }

        // Family-scoped access for non-superadmin users
        return $externalMember->family_no === ($user->family_no ?? $externalMember->family_no);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }
        
        return $user->can('create-external-member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExternalMember $externalMember): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }
        
        if (!$user->can('update-external-member')) {
            return false;
        }

        // Family-scoped access for non-superadmin users
        return $externalMember->family_no === ($user->family_no ?? $externalMember->family_no);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExternalMember $externalMember): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }
        
        if (!$user->can('delete-external-member')) {
            return false;
        }

        // Family-scoped access for non-superadmin users
        return $externalMember->family_no === ($user->family_no ?? $externalMember->family_no);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExternalMember $externalMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('restore-external-member') && 
               $externalMember->family_no === ($user->family_no ?? $externalMember->family_no);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExternalMember $externalMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('force-delete-external-member') && 
               $externalMember->family_no === ($user->family_no ?? $externalMember->family_no);
    }
}
