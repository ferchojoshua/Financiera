<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\LoanApplication;
use App\Policies\LoanApplicationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        LoanApplication::class => LoanApplicationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Definir el permiso de acceso al área de administración
        Gate::define('access-admin-area', function ($user) {
            return $user->isAdmin() || $user->isSuperAdmin();
        });
    }
}
