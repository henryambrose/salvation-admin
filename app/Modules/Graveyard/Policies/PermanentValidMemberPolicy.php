<?php

namespace Modules\Graveyard\Policies;

use App\Models\User;
use Modules\Graveyard\Models\PermanentValidMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermanentValidMemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-permanent-valid-member');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PermanentValidMember $permanentValidMember): bool
    {
        return $user->can('read-permanent-valid-member');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-permanent-valid-member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PermanentValidMember $permanentValidMember): bool
    {
        return $user->can('update-permanent-valid-member');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PermanentValidMember $permanentValidMember): bool
    {
        return $user->can('delete-permanent-valid-member');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PermanentValidMember $permanentValidMember): bool
    {
        return $user->can('restore-permanent-valid-member');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PermanentValidMember $permanentValidMember): bool
    {
        return $user->can('delete-permanent-valid-member');
    }
}
