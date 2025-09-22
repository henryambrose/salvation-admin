<?php

namespace Modules\Fund\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register any bindings or singletons here

        // Register the Fund module's AuthServiceProvider
        $this->app->register(AuthServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(base_path('routes/fund.php'));
        
        // Load migrations
        $this->loadMigrationsFrom(database_path('modules/fund/database/migrations'));
    }
}
