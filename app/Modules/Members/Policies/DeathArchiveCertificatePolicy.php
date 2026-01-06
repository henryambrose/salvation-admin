<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\User;
use Modules\Members\Models\DeathArchiveCertificate;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeathArchiveCertificatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any death archive certificates.
     * Uses same permission as regular certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-certificate');
    }

    /**
     * Determine whether the user can view the death archive certificate.
     * Uses same permission as regular certificates.
     */
    public function view(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('read-certificate');
    }

    /**
     * Determine whether the user can create death archive certificates.
     * Uses same permission as regular certificates.
     */
    public function create(User $user): bool
    {
        return $user->can('create-certificate');
    }

    /**
     * Determine whether the user can update the death archive certificate.
     * Uses same permission as regular certificates.
     */
    public function update(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('update-certificate');
    }

    /**
     * Determine whether the user can delete the death archive certificate.
     * Uses same permission as regular certificates.
     */
    public function delete(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('delete-certificate');
    }

    /**
     * Determine whether the user can restore the death archive certificate.
     * Uses same permission as regular certificates.
     */
    public function restore(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('restore-certificate');
    }

    /**
     * Determine whether the user can download the death archive certificate.
     * Uses same permission as regular certificates.
     */
    public function download(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('download-certificate');
    }

    /**
     * Determine whether the user can permanently delete the death archive certificate.
     * Uses same permission as regular certificates.
     */
    public function forceDelete(User $user, DeathArchiveCertificate $certificate): bool
    {
        return $user->can('delete-certificate') && $user->hasRole('super admin');
    }
}
