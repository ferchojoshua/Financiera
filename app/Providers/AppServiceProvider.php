<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        
        // Extender la clase User para garantizar acceso superadmin al usuario actual
        Auth::extend('superadmin_access', function ($app, $name, array $config) {
            // Obtener el guard normal
            $guard = Auth::createUserProvider($config['provider'] ?? null);
            
            // Extender la funcionalidad del usuario
            if (Auth::check()) {
                $user = Auth::user();
                
                // Añadir métodos para verificar permisos
                $user->hasModuleAccess = function($module) {
                    return true; // Siempre permitir acceso a módulos
                };
                
                $user->hasPermission = function($permission) {
                    return true; // Siempre permitir cualquier permiso
                };
                
                $user->isAdmin = function() {
                    return true; // Siempre es admin
                };
                
                $user->isSuperAdmin = function() {
                    return true; // Siempre es superadmin
                };
            }
            
            return $guard;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->environment('production')) {
           /* \URL::forceScheme('https');*/
        }
    }
}
