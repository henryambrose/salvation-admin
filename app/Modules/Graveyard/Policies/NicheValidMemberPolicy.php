<?php

namespace Modules\Graveyard\Policies;

use App\Models\User;
use Modules\Graveyard\Models\NicheValidMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class NicheValidMemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-niche-valid-member');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NicheValidMember $nicheValidMember): bool
    {
        return $user->can('read-niche-valid-member');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-niche-valid-member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NicheValidMember $nicheValidMember): bool
    {
        return $user->can('update-niche-valid-member');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NicheValidMember $nicheValidMember): bool
    {
        return $user->can('delete-niche-valid-member');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, NicheValidMember $nicheValidMember): bool
    {
        return $user->can('restore-niche-valid-member');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, NicheValidMember $nicheValidMember): bool
    {
        return $user->can('delete-niche-valid-member');
    }
}
