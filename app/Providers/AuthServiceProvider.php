<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\\Models\\Model' => 'App\\Policies\\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate definitions that map to role checks (Spatie)
        Gate::define('manage-system', function ($user) {
            return $user->hasRole('super_admin');
        });

        Gate::define('manage-viex', function ($user) {
            return $user->hasRole('viex_admin') || $user->hasRole('super_admin');
        });

        Gate::define('manage-dean', function ($user) {
            return $user->hasRole('decano_director') || $user->hasRole('super_admin');
        });

        Gate::define('coordinate-works', function ($user) {
            return $user->hasRole('coordinador_extension') || $user->hasRole('super_admin');
        });

        Gate::define('manage-own-works', function ($user) {
            return $user->hasRole('profesor') || $user->hasRole('super_admin');
        });

        Log::info('AuthServiceProvider: Gates registered for menu permissions');
    }
}
