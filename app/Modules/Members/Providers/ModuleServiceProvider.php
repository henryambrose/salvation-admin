<?php

namespace Modules\Members\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register facades
        $this->app->bind('api-response', \Modules\Members\Services\ApiResponseService::class);
        
        // Register helpers
        require_once __DIR__ . '/../Helpers/AuditHelper.php';
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/web.php'));
        $this->loadRoutesFrom(base_path('routes/auth.php'));
        $this->loadRoutesFrom(base_path('routes/audit.php'));
        $this->loadRoutesFrom(base_path('routes/settings.php'));
        $this->loadRoutesFrom(base_path('routes/users.php'));
        $this->loadRoutesFrom(base_path('routes/age_group.php'));
        $this->loadRoutesFrom(base_path('routes/blood_group.php'));
        $this->loadRoutesFrom(base_path('routes/cells_and_association.php'));
        $this->loadRoutesFrom(base_path('routes/cells_and_association_members.php'));
        $this->loadRoutesFrom(base_path('routes/city.php'));
        $this->loadRoutesFrom(base_path('routes/clusters.php'));
        $this->loadRoutesFrom(base_path('routes/community.php'));
        $this->loadRoutesFrom(base_path('routes/community_clusters.php'));
        $this->loadRoutesFrom(base_path('routes/country.php'));
        $this->loadRoutesFrom(base_path('routes/designation.php'));
        $this->loadRoutesFrom(base_path('routes/external_members.php'));
        $this->loadRoutesFrom(base_path('routes/family_income_range.php'));
        $this->loadRoutesFrom(base_path('routes/family_tree.php'));
        $this->loadRoutesFrom(base_path('routes/gender.php'));
        $this->loadRoutesFrom(base_path('routes/income_range.php'));
        $this->loadRoutesFrom(base_path('routes/member.php'));
        $this->loadRoutesFrom(base_path('routes/p_p_c_head.php'));
        $this->loadRoutesFrom(base_path('routes/parish.php'));
        $this->loadRoutesFrom(base_path('routes/relationship.php'));
        $this->loadRoutesFrom(base_path('routes/s_c_c_head.php'));
        $this->loadRoutesFrom(base_path('routes/state.php'));
        $this->loadRoutesFrom(base_path('routes/status.php'));
        $this->loadRoutesFrom(base_path('routes/town.php'));
        $this->loadRoutesFrom(base_path('routes/zones.php'));
        
        $this->loadMigrationsFrom(database_path('modules/members/database/migrations'));
        $this->loadPolicies();
        $this->loadCommands();
    }

    protected function loadPolicies(): void
    {
        $policies = [
            \Modules\Members\Models\Member::class => \Modules\Members\Policies\MemberPolicy::class,
            \Modules\Members\Models\ExternalMember::class => \Modules\Members\Policies\ExternalMemberPolicy::class,
            \Spatie\Permission\Models\Role::class => \Modules\Members\Policies\RolePolicy::class,
        ];

        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    protected function loadCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\Members\Console\Commands\CheckParishes::class,
                \Modules\Members\Console\Commands\GenerateAuditTriggers::class,
                \Modules\Members\Console\Commands\ListUsers::class,
                // Add other commands as needed
            ]);
        }
    }
}
