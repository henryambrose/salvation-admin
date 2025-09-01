<?php

namespace Modules\Graveyard\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $namespace = 'Modules\Graveyard\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
        
        $this->loadMigrationsFrom(database_path('modules/graveyard/database/migrations'));
        $this->loadPolicies();
        $this->loadCommands();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        $this->loadRoutesFrom(base_path('routes/graveyard.php'));
    }

    protected function loadPolicies(): void
    {
        $policies = [
            // \Modules\Graveyard\Models\Cemetery::class => \Modules\Graveyard\Policies\CemeteryPolicy::class,
            // \Modules\Graveyard\Models\Section::class => \Modules\Graveyard\Policies\SectionPolicy::class,
            // \Modules\Graveyard\Models\Grave::class => \Modules\Graveyard\Policies\GravePolicy::class,
            // \Modules\Graveyard\Models\Burial::class => \Modules\Graveyard\Policies\BurialPolicy::class,
            // \Modules\Graveyard\Models\MaintenanceRecord::class => \Modules\Graveyard\Policies\MaintenanceRecordPolicy::class,
            // \Modules\Graveyard\Models\FinancialTransaction::class => \Modules\Graveyard\Policies\FinancialTransactionPolicy::class,
            // \Modules\Graveyard\Models\Visitor::class => \Modules\Graveyard\Policies\VisitorPolicy::class,
        ];

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
