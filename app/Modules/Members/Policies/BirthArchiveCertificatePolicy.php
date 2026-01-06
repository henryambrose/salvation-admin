<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\User;
use Modules\Members\Models\BirthArchiveCertificate;
use Illuminate\Auth\Access\HandlesAuthorization;

class BirthArchiveCertificatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any birth archive certificates.
     * Uses same permission as regular certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-certificate');
    }

    /**
     * Determine whether the user can view the birth archive certificate.
     * Uses same permission as regular certificates.
     */
    public function view(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('read-certificate');
    }

    /**
     * Determine whether the user can create birth archive certificates.
     * Uses same permission as regular certificates.
     */
    public function create(User $user): bool
    {
        return $user->can('create-certificate');
    }

    /**
     * Determine whether the user can update the birth archive certificate.
     * Uses same permission as regular certificates.
     */
    public function update(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('update-certificate');
    }

    /**
     * Determine whether the user can delete the birth archive certificate.
     * Uses same permission as regular certificates.
     */
    public function delete(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('delete-certificate');
    }

    /**
     * Determine whether the user can restore the birth archive certificate.
     * Uses same permission as regular certificates.
     */
    public function restore(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('restore-certificate');
    }

    /**
     * Determine whether the user can download the birth archive certificate.
     * Uses same permission as regular certificates.
     */
    public function download(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('download-certificate');
    }

    /**
     * Determine whether the user can permanently delete the birth archive certificate.
     * Uses same permission as regular certificates.
     */
    public function forceDelete(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('delete-certificate') && $user->hasRole('super admin');
    }
}
