<?php

namespace Modules\Graveyard\Policies;

use Modules\Members\Models\User;
use Modules\Graveyard\Models\ObituaryPage;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObituaryPagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any obituary pages.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('read-obituary-page');
    }

    /**
     * Determine whether the user can view the obituary page.
     */
    public function view(User $user, ObituaryPage $obituaryPage): bool
    {
        return $user->hasPermissionTo('read-obituary-page');
    }

    /**
     * Determine whether the user can create obituary pages.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-obituary-page');
    }

    /**
     * Determine whether the user can update the obituary page.
     */
    public function update(User $user, ObituaryPage $obituaryPage): bool
    {
        return $user->hasPermissionTo('update-obituary-page');
    }

    /**
     * Determine whether the user can delete the obituary page.
     */
    public function delete(User $user, ObituaryPage $obituaryPage): bool
    {
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
        return $user->hasPermissionTo('update-obituary-page') || $user->hasRole('admin');
    }
}