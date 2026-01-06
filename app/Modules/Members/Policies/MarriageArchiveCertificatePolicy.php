<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\User;
use Modules\Members\Models\MarriageArchiveCertificate;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarriageArchiveCertificatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any marriage archive certificates.
     * Uses same permission as regular certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-certificate');
    }

    /**
     * Determine whether the user can view the marriage archive certificate.
     * Uses same permission as regular certificates.
     */
    public function view(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('read-certificate');
    }

    /**
     * Determine whether the user can create marriage archive certificates.
     * Uses same permission as regular certificates.
     */
    public function create(User $user): bool
    {
        return $user->can('create-certificate');
    }

    /**
     * Determine whether the user can update the marriage archive certificate.
     * Uses same permission as regular certificates.
     */
    public function update(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('update-certificate');
    }

    /**
     * Determine whether the user can delete the marriage archive certificate.
     * Uses same permission as regular certificates.
     */
    public function delete(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('delete-certificate');
    }

    /**
     * Determine whether the user can restore the marriage archive certificate.
     * Uses same permission as regular certificates.
     */
    public function restore(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('restore-certificate');
    }

    /**
     * Determine whether the user can download the marriage archive certificate.
     * Uses same permission as regular certificates.
     */
    public function download(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('download-certificate');
    }

    /**
     * Determine whether the user can permanently delete the marriage archive certificate.
     * Uses same permission as regular certificates.
     */
    public function forceDelete(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('delete-certificate') && $user->hasRole('super admin');
    }
}
