<?php

namespace App\Providers;

use  Modules\Graveyard\Policies\ObituaryPolicy;
use Modules\Graveyard\Policies\ObituaryPlanPolicy;
use Modules\Graveyard\Models\ObituaryPage;
use Modules\Graveyard\Models\ObituaryPlan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            // Allow super admins to pass any permission check
            return $user->is_superadmin ? true : null;
        });
        // Register policies
        Gate::policy(ObituaryPage::class, ObituaryPolicy::class);
        Gate::policy(ObituaryPlan::class, ObituaryPlanPolicy::class);
    }
}
