<?php

namespace Modules\Members\Providers;

use Modules\Members\Models\CellsAndAssociation;
use Modules\Members\Models\CellsAndAssociationMember;
use Modules\Members\Models\City;
use Modules\Members\Models\Community;
use Modules\Members\Models\CommunityCluster;
use Modules\Members\Models\CommunityFund;
use Modules\Members\Models\Designation;
use Modules\Members\Models\ExternalMember;
use Modules\Members\Models\Member;
use Modules\Members\Models\Parish;
use Modules\Members\Models\PPCHead;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\SCCHead;
use Modules\Members\Models\User;
use Modules\Members\Models\CertificateRecord;
use Modules\Members\Models\CertificateType;
use Modules\Members\Models\CertificateTemplate;
use Modules\Members\Policies\CellsAndAssociationPolicy;
use Modules\Members\Policies\CellsAndAssociationMemberPolicy;
use Modules\Members\Policies\CityPolicy;
use Modules\Members\Policies\CommunityPolicy;
use Modules\Members\Policies\CommunityClusterPolicy;
use Modules\Members\Policies\CommunityFundPolicy;
use Modules\Members\Policies\DesignationPolicy;
use Modules\Members\Policies\ExternalMemberPolicy;
use Modules\Members\Policies\MemberPolicy;
use Modules\Members\Policies\ParishPolicy;
use Modules\Members\Policies\PPCHeadPolicy;
use Modules\Members\Policies\RelationshipPolicy;
use Modules\Members\Policies\SCCHeadPolicy;
use Modules\Members\Policies\UserPolicy;
use Modules\Members\Policies\RolePolicy;
use Modules\Members\Policies\CertificateRecordPolicy;
use Modules\Members\Policies\CertificateTypePolicy;
use Modules\Members\Policies\CertificateTemplatePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        CellsAndAssociation::class => CellsAndAssociationPolicy::class,
        CellsAndAssociationMember::class => CellsAndAssociationMemberPolicy::class,
        City::class => CityPolicy::class,
        Community::class => CommunityPolicy::class,
        CommunityCluster::class => CommunityClusterPolicy::class,
        Designation::class => DesignationPolicy::class,
        ExternalMember::class => ExternalMemberPolicy::class,
        Member::class => MemberPolicy::class,
        Parish::class => ParishPolicy::class,
        PPCHead::class => PPCHeadPolicy::class,
        Relationship::class => RelationshipPolicy::class,
        SCCHead::class => SCCHeadPolicy::class,
        User::class => UserPolicy::class,
        \Spatie\Permission\Models\Role::class => RolePolicy::class,
        CertificateRecord::class => CertificateRecordPolicy::class,
        CertificateType::class => CertificateTypePolicy::class,
        CertificateTemplate::class => CertificateTemplatePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
