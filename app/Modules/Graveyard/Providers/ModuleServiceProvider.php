<?php

namespace Modules\Graveyard\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register any bindings or singletons here
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(base_path('routes/graveyard.php'));

        // Load migrations
        $this->loadMigrationsFrom(database_path('modules/graveyard/database/migrations'));

        $this->loadPolicies();
        $this->loadCommands();
    }

    protected function loadPolicies(): void
    {
        $policies = [];

        foreach ($policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    protected function loadCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Register Graveyard module commands here
            ]);
        }
    }
}
