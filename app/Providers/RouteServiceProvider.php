<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapModuleRoutes();
        
        // Auto-descubrimiento de rutas
        if (config('app.auto_discover_routes', true)) {
            $this->autoDiscoverRoutes();
        }
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define routes for modules.
     *
     * @return void
     */
    protected function mapModuleRoutes()
    {
        if (File::exists(base_path('routes/modules'))) {
            $files = File::files(base_path('routes/modules'));
            
            foreach ($files as $file) {
                Route::middleware('web')
                     ->namespace($this->namespace)
                     ->group($file->getPathname());
            }
        }
    }

    /**
     * Auto-descubrir rutas y registrarlas en la base de datos
     *
     * @return void
     */
    protected function autoDiscoverRoutes()
    {
        try {
            // Verificar que existe la tabla de módulos
            if (!Schema::hasTable('modules')) {
                return;
            }
            
            // Recopilar todas las rutas definidas
            $routes = Route::getRoutes();
            $registeredRoutes = [];
            
            foreach ($routes as $route) {
                // Solo procesar rutas con nombre
                if (!$route->getName()) {
                    continue;
                }
                
                $routeName = $route->getName();
                $routeUri = $route->uri();
                $routeMethods = implode('|', $route->methods());
                
                // Determinar el módulo basado en el nombre de la ruta
                $moduleName = $this->determineModuleFromRoute($routeName);
                
                if ($moduleName) {
                    $registeredRoutes[] = [
                        'name' => $routeName,
                        'uri' => $routeUri,
                        'methods' => $routeMethods,
                        'module' => $moduleName
                    ];
                }
            }
            
            // Registrar rutas en la base de datos si no existen
            if (count($registeredRoutes) > 0) {
                $this->syncRoutesWithDatabase($registeredRoutes);
            }
        } catch (\Exception $e) {
            Log::error('Error en auto-descubrimiento de rutas: ' . $e->getMessage());
        }
    }

    /**
     * Determinar el módulo al que pertenece una ruta basado en su nombre
     *
     * @param string $routeName
     * @return string|null
     */
    protected function determineModuleFromRoute($routeName)
    {
        // Extraer el prefijo del nombre de la ruta (antes del primer punto)
        $parts = explode('.', $routeName);
        $prefix = $parts[0] ?? null;
        
        if (!$prefix) {
            return null;
        }
        
        // Mapeo de prefijos de rutas a nombres de módulos
        $moduleMap = [
            'client' => 'clientes',
            'clients' => 'clientes',
            'credits' => 'creditos',
            'credit' => 'creditos',
            'loans' => 'creditos',
            'payment' => 'pagos',
            'payments' => 'pagos',
            'route' => 'rutas',
            'routes' => 'rutas',
            'agent' => 'agentes',
            'agents' => 'agentes',
            'user' => 'usuarios',
            'users' => 'usuarios',
            'report' => 'reportes',
            'reports' => 'reportes',
            'admin' => 'admin',
            'setting' => 'configuracion',
            'settings' => 'configuracion',
            'dashboard' => 'dashboard',
            'role' => 'roles',
            'roles' => 'roles'
        ];
        
        return $moduleMap[$prefix] ?? $prefix;
    }

    /**
     * Sincronizar las rutas descubiertas con la base de datos
     *
     * @param array $routes
     * @return void
     */
    protected function syncRoutesWithDatabase($routes)
    {
        try {
            // Obtener módulos existentes
            $existingModules = DB::table('modules')->pluck('id', 'name')->toArray();
            
            // Crear módulos que no existen
            $modulesToCreate = [];
            foreach ($routes as $route) {
                if (!isset($existingModules[$route['module']])) {
                    $modulesToCreate[$route['module']] = $route['module'];
                }
            }
            
            foreach ($modulesToCreate as $moduleName) {
                DB::table('modules')->insert([
                    'name' => $moduleName,
                    'description' => ucfirst($moduleName),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Actualizar la lista de módulos existentes
            $existingModules = DB::table('modules')->pluck('id', 'name')->toArray();
            
            // Obtener rutas existentes
            $existingRoutes = DB::table('routes')->pluck('id', 'name')->toArray();
            
            // Crear o actualizar rutas
            foreach ($routes as $route) {
                $moduleId = $existingModules[$route['module']] ?? null;
                
                if (!$moduleId) {
                    continue;
                }
                
                $routeData = [
                    'name' => $route['name'],
                    'uri' => $route['uri'],
                    'methods' => $route['methods'],
                    'module_id' => $moduleId,
                    'updated_at' => now()
                ];
                
                if (isset($existingRoutes[$route['name']])) {
                    // Actualizar ruta existente
                    DB::table('routes')
                        ->where('id', $existingRoutes[$route['name']])
                        ->update($routeData);
                } else {
                    // Crear nueva ruta
                    $routeData['created_at'] = now();
                    DB::table('routes')->insert($routeData);
                }
            }
            
            // Opcional: Limpiar rutas antiguas que ya no existen
            if (config('app.clean_obsolete_routes', false)) {
                $existingRouteNames = array_column($routes, 'name');
                DB::table('routes')
                    ->whereNotIn('name', $existingRouteNames)
                    ->delete();
            }
        } catch (\Exception $e) {
            Log::error('Error sincronizando rutas con la base de datos: ' . $e->getMessage());
        }
    }
}
