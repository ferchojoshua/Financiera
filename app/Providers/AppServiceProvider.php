<?php

namespace App\Providers;

use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

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
        
        // Registrar directorio de componentes Blade
        Blade::componentNamespace('App\\View\\Components', 'app');
        
        // Registrar directivas de Blade para verificar roles y permisos
        Blade::directive('role', function ($expression) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::hasRole({$expression})): ?>";
        });
        
        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });
        
        Blade::directive('hasaccess', function ($expression) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::hasModuleAccess({$expression})): ?>";
        });
        
        Blade::directive('endhasaccess', function () {
            return "<?php endif; ?>";
        });
        
        // SOLUCIÓN PARA EL PROBLEMA DE LAYOUT EN CLIENTS
        // Forzar uso del layout master en vistas de clientes
        View::composer('clients.*', function ($view) {
            $view->with('_layout', 'layouts.master');
        });
        
        // Interceptar la renderización de vistas para clientes
        View::composer('*', function ($view) {
            // Inyectar el contador de créditos pendientes en todas las vistas
            if (Auth::check() && (Auth::user()->hasRole('supervisor') || Auth::user()->hasRole('admin'))) {
                $pending_credits_count = DB::table('credit')->where('status', 'pendiente')->count();
            } else {
                $pending_credits_count = 0;
            }
            $view->with('pending_credits_count', $pending_credits_count);

            // Mantener la lógica existente para forzar layout
            $viewName = $view->getName();
            if (strpos($viewName, 'clients.') === 0) {
                $view->with('_layout', 'layouts.master');
            }
        });
        
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
