<?php

namespace Modules\Graveyard\Policies;

use Modules\Members\Models\User;
use Modules\Graveyard\Models\ObituaryPage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class ObituaryPagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any obituary pages.
     */
    public function viewAny(User $user): bool
    {
        // Super admins can always view obituary pages
        if ($user->is_superadmin) {
            return true;
        }

        return $user->hasPermissionTo('read-obituary-page');
    }

    /**
     * Determine whether the user can view the obituary page.
     */
    public function view(User $user, ObituaryPage $obituaryPage): bool
    {
        // Super admins can always view obituary pages
        if ($user->is_superadmin) {
            return true;
        }

        return $user->hasPermissionTo('read-obituary-page');
    }

    /**
     * Determine whether the user can create obituary pages.
     */
    public function create(User $user): bool
    {
        // Super admins can always create obituary pages
        if ($user->is_superadmin) {
            return true;
        }

        return $user->hasPermissionTo('create-obituary-page');
    }

    /**
     * Determine whether the user can update the obituary page.
     */
    public function update(User $user, ObituaryPage $obituaryPage): bool
    {
        // Super admins can always update obituary pages
        if ($user->is_superadmin) {
            return true;
        }

        return $user->hasPermissionTo('update-obituary-page');
    }

    /**
     * Determine whether the user can delete the obituary page.
     */
    public function delete(User $user, ObituaryPage $obituaryPage): bool
    {
        // Super admins can always delete obituary pages
        if ($user->is_superadmin) {
            return true;
        }

        return $user->hasPermissionTo('delete-obituary-page');
    }

    /**
     * Determine whether the user can restore the obituary page.
     */
    public function restore(User $user, ObituaryPage $obituaryPage): bool
    {
        return $user->hasPermissionTo('update-obituary-page');
    }

    /**
     * Determine whether the user can permanently delete the obituary page.
     */
    public function forceDelete(User $user, ObituaryPage $obituaryPage): bool
    {
        return $user->hasPermissionTo('delete-obituary-page');
    }

    /**
     * Determine whether the user can publish/unpublish obituary pages.
     */
    public function publish(User $user, ObituaryPage $obituaryPage): bool
    {
        // Super admins can always publish obituary pages
        if ($user->is_superadmin) {
            return true;
        }

        return $user->hasPermissionTo('publish-obituary-page');
    }

    /**
     * Determine whether the user can preview obituary pages.
     */
    public function preview(User $user, ObituaryPage $obituaryPage): bool
    {
        return $user->hasPermissionTo('read-obituary-page');
    }

    /**
     * Determine whether the user can manage files for obituary pages.
     */
    public function manageFiles(User $user): bool
    {
        // Super admins can always manage files
        if ($user->is_superadmin) {
            return true;
        }

        return $user->hasPermissionTo('update-obituary-page') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can process payments for obituary pages.
     */
    public function processPayments(User $user): bool
    {
        // Super admins can always process payments
        if ($user->is_superadmin) {
            return true;
        }

        return $user->hasPermissionTo('update-obituary-page');
    }

    /**
     * Determine whether the user can manage condolences.
     */
    public function manageCondolences(User $user): bool
    {
        return $user->hasPermissionTo('update-obituary-page');
    }

    /**
     * Determine whether the user can approve condolences.
     */
    public function approveCondolences(User $user): bool
    {
        return $user->hasPermissionTo('update-obituary-page');
    }

    /**
     * Determine whether the user can reject condolences.
     */
    public function rejectCondolences(User $user): bool
    {
        return $user->hasPermissionTo('update-obituary-page');
    }

    /**
     * Determine whether the user can generate QR codes.
     */
    public function generateQrCode(User $user, ObituaryPage $obituaryPage): bool
    {
        return $user->hasPermissionTo('read-obituary-page');
    }
}
