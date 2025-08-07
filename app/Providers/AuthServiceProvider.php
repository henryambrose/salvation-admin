<?php

namespace App\Providers;

use App\Models\User;
use App\Models\ExternalMember;
use App\Policies\UserPolicy;
use App\Policies\ExternalMemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        ExternalMember::class => ExternalMemberPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
} 