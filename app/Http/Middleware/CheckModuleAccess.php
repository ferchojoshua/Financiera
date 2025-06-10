<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $module
     * @return mixed
     */
    public function handle($request, Closure $next, $module = null)
    {
        // Si no hay usuario autenticado, redirigir al login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Si el usuario puede omitir verificaciones, permitir acceso
        if ($user->shouldBypassPermissionChecks()) {
            return $next($request);
        }
        
        try {
            // Identificar el módulo al que pertenece la ruta actual
            if (!$module) {
                $currentRouteName = Route::currentRouteName();
                $module = $this->determineModuleFromRoute($currentRouteName);
                
                // Si no se pudo determinar el módulo, denegar acceso
                if (!$module) {
                    Log::warning("No se pudo determinar el módulo para la ruta: {$currentRouteName}");
                    return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
                }
            }
            
            // Verificar si el usuario tiene acceso al módulo
            if ($user->hasModuleAccess($module)) {
                return $next($request);
            }
            
            // Si es una solicitud AJAX, devolver error 403
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a este módulo.'], 403);
            }
            
            // Registrar intento de acceso no autorizado
            Log::warning("Usuario {$user->id} ({$user->name}) intentó acceder al módulo {$module} sin permiso");
            
            // Redirigir a la página de acceso denegado
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        
        } catch (\Exception $e) {
            // Registrar error
            Log::error("Error verificando permisos: " . $e->getMessage());
            return redirect()->route('home')->with('error', 'Error al verificar permisos de acceso.');
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
        // Si no hay nombre de ruta, no se puede determinar el módulo
        if (!$routeName) {
            return null;
        }
        
        try {
            // Buscar la ruta en la base de datos
            $route = DB::table('routes')
                ->where('name', $routeName)
                ->first();
                
            if ($route) {
                // Obtener el módulo asociado a la ruta
                $module = DB::table('modules')
                    ->where('id', $route->module_id)
                    ->first();
                    
                if ($module) {
                    return $module->name;
                }
            }
            
            // Si no se encontró la ruta en la base de datos, intentar determinar
            // el módulo a partir del prefijo del nombre de la ruta
            $parts = explode('.', $routeName);
            $prefix = $parts[0] ?? null;
            
            if (!$prefix) {
                return null;
            }
            
            // Mapeo de prefijos de rutas a nombres de módulos
            $moduleMap = [
                'client' => 'clientes',
                'clients' => 'clientes',
                'credit' => 'creditos',
                'credits' => 'creditos',
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
        } catch (\Exception $e) {
            // Si hay algún error, registrarlo y devolver null
            Log::error("Error determinando módulo para la ruta {$routeName}: " . $e->getMessage());
            return null;
        }
    }
}
