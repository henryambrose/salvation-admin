<?php

namespace Modules\Members\Providers;

use Modules\Members\Models\Community;
use Modules\Members\Models\CommunityFund;
use Modules\Members\Models\ExternalMember;
use Modules\Members\Models\Member;
use Modules\Members\Models\CertificateRecord;
use Modules\Members\Models\CertificateType;
use Modules\Members\Models\CertificateTemplate;
use Modules\Members\Models\BirthArchiveCertificate;
use Modules\Members\Models\MarriageArchiveCertificate;
use Modules\Members\Models\DeathArchiveCertificate;
use Modules\Members\Policies\CommunityPolicy;
use Modules\Members\Policies\CommunityFundPolicy;
use Modules\Members\Policies\ExternalMemberPolicy;
use Modules\Members\Policies\MemberPolicy;
use Modules\Members\Policies\RolePolicy;
use Modules\Members\Policies\CertificateRecordPolicy;
use Modules\Members\Policies\CertificateTypePolicy;
use Modules\Members\Policies\CertificateTemplatePolicy;
use Modules\Members\Policies\BirthArchiveCertificatePolicy;
use Modules\Members\Policies\MarriageArchiveCertificatePolicy;
use Modules\Members\Policies\DeathArchiveCertificatePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Community::class => CommunityPolicy::class,
        ExternalMember::class => ExternalMemberPolicy::class,
        Member::class => MemberPolicy::class,
        \Spatie\Permission\Models\Role::class => RolePolicy::class,
        CertificateRecord::class => CertificateRecordPolicy::class,
        CertificateType::class => CertificateTypePolicy::class,
        CertificateTemplate::class => CertificateTemplatePolicy::class,
        BirthArchiveCertificate::class => BirthArchiveCertificatePolicy::class,
        MarriageArchiveCertificate::class => MarriageArchiveCertificatePolicy::class,
        DeathArchiveCertificate::class => DeathArchiveCertificatePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
