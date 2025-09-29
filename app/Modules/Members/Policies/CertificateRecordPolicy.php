<?php

namespace Modules\Members\Policies;

use App\Models\User;
use Modules\Members\Models\CertificateRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificateRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any certificate records.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-certificate') || $user->can('view-certificate-history');
    }

    /**
     * Determine whether the user can view the certificate record.
     */
    public function view(User $user, CertificateRecord $certificateRecord): bool
    {
        return $user->can('read-certificate') || $user->can('view-certificate-history');
    }

    /**
     * Determine whether the user can create certificate records.
     */
    public function create(User $user): bool
    {
        return $user->can('create-certificate') || $user->can('generate-certificate');
    }

    /**
     * Determine whether the user can update the certificate record.
     */
    public function update(User $user, CertificateRecord $certificateRecord): bool
    {
        return $user->can('update-certificate');
    }

    /**
     * Determine whether the user can delete the certificate record.
     */
    public function delete(User $user, CertificateRecord $certificateRecord): bool
    {
        return $user->can('delete-certificate');
    }

    /**
     * Determine whether the user can restore the certificate record.
     */
    public function restore(User $user, CertificateRecord $certificateRecord): bool
    {
        return $user->can('restore-certificate');
    }

    /**
     * Determine whether the user can permanently delete the certificate record.
     */
    public function forceDelete(User $user, CertificateRecord $certificateRecord): bool
    {
        return $user->can('delete-certificate') && $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can download the certificate.
     */
    public function download(User $user, CertificateRecord $certificateRecord): bool
    {
        return $user->can('download-certificate');
    }

    /**
     * Determine whether the user can reprint the certificate.
     */
    public function reprint(User $user, CertificateRecord $certificateRecord): bool
    {
        return $user->can('reprint-certificate');
    }

    /**
     * Determine whether the user can generate certificates.
     */
    public function generate(User $user): bool
    {
        return $user->can('generate-certificate');
    }
}