<?php

namespace Modules\Members\Policies;

use App\Models\User;
use Modules\Members\Models\DeathArchiveCertificate;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeathArchiveCertificatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any death archive certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-death-archive');
    }

    /**
     * Determine whether the user can view the death archive certificate.
     */
    public function view(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('read-death-archive');
    }

    /**
     * Determine whether the user can create death archive certificates.
     */
    public function create(User $user): bool
    {
        return $user->can('create-death-archive');
    }

    /**
     * Determine whether the user can update the death archive certificate.
     */
    public function update(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('update-death-archive');
    }

    /**
     * Determine whether the user can delete the death archive certificate.
     */
    public function delete(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('delete-death-archive');
    }

    /**
     * Determine whether the user can restore the death archive certificate.
     */
    public function restore(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('restore-death-archive');
    }

    /**
     * Determine whether the user can download the death archive certificate.
     */
    public function download(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('download-death-archive');
    }

    /**
     * Determine whether the user can permanently delete the death archive certificate.
     */
    public function forceDelete(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('delete-death-archive') && $user->hasRole('super admin');
    }
}
