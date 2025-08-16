<?php

namespace App\Providers;

use App\Models\BloodGroup;
use App\Models\CellsAndAssociation;
use App\Models\CellsAndAssociationMember;
use App\Models\City;
use App\Models\Community;
use App\Models\CommunityCluster;
use App\Models\CommunityFund;
use App\Models\Designation;
use App\Models\ExternalMember;
use App\Models\IncomeRange;
use App\Models\Member;
use App\Models\Parish;
use App\Models\PPCHead;
use App\Models\Relationship;
use App\Models\SCCHead;
use App\Models\User;
use App\Models\Zone;
use App\Policies\BloodGroupPolicy;
use App\Policies\CellsAndAssociationPolicy;
use App\Policies\CellsAndAssociationMemberPolicy;
use App\Policies\CityPolicy;
use App\Policies\CommunityPolicy;
use App\Policies\CommunityClusterPolicy;
use App\Policies\CommunityFundPolicy;
use App\Policies\DesignationPolicy;
use App\Policies\ExternalMemberPolicy;
use App\Policies\IncomeRangePolicy;
use App\Policies\MemberPolicy;
use App\Policies\ParishPolicy;
use App\Policies\PPCHeadPolicy;
use App\Policies\RelationshipPolicy;
use App\Policies\SCCHeadPolicy;
use App\Policies\UserPolicy;
use App\Policies\ZonePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        BloodGroup::class => BloodGroupPolicy::class,
        CellsAndAssociation::class => CellsAndAssociationPolicy::class,
        CellsAndAssociationMember::class => CellsAndAssociationMemberPolicy::class,
        City::class => CityPolicy::class,
        Community::class => CommunityPolicy::class,
        CommunityCluster::class => CommunityClusterPolicy::class,
        CommunityFund::class => CommunityFundPolicy::class,
        Designation::class => DesignationPolicy::class,
        ExternalMember::class => ExternalMemberPolicy::class,
        IncomeRange::class => IncomeRangePolicy::class,
        Member::class => MemberPolicy::class,
        Parish::class => ParishPolicy::class,
        PPCHead::class => PPCHeadPolicy::class,
        Relationship::class => RelationshipPolicy::class,
        SCCHead::class => SCCHeadPolicy::class,
        User::class => UserPolicy::class,
        Zone::class => ZonePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
