<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $module  El módulo que se intenta acceder
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $module)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }
        
        // Verificar si debe omitir verificaciones
        if ($user->shouldBypassPermissionChecks()) {
            return $next($request);
        }
        
        // Superadmin y admin siempre tienen acceso
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return $next($request);
        }
        
        try {
            // Obtener el rol del usuario
            $role = DB::table('roles')->where('slug', $user->role)->first();
            
            if (!$role) {
                // Si no encuentra rol por slug, intentar por level (compatibilidad con código antiguo)
                $role = DB::table('roles')->where('level', $user->level)->first();
                
                if (!$role) {
                    Log::warning("Usuario ID {$user->id} sin rol válido intentando acceder a {$module}");
                    return redirect('/home')->with('error', 'No tienes permisos para acceder a esta sección.');
                }
            }
            
            // Verificar permiso en la tabla role_module_permissions
            $permission = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->where('module', $module)
                ->where('has_access', true)
                ->first();
                
            if ($permission) {
                return $next($request);
            }
            
            // Si no tiene permiso específico, verificar si tiene permisos administrativos
            $adminPermission = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->where('module', 'admin')
                ->where('has_access', true)
                ->first();
                
            if ($adminPermission) {
                return $next($request);
            }
            
            // En modo debug, permitir acceso y registrar
            if (config('app.debug')) {
                Log::info("DEBUG MODE: Permitiendo acceso a {$module} para usuario {$user->id} con rol {$user->role}");
                return $next($request);
            }
            
            // No tiene permisos para este módulo
            Log::info("Usuario ID {$user->id} con rol {$role->slug} sin permiso para acceder a {$module}");
            return redirect('/home')->with('error', 'No tienes permisos para acceder a este módulo.');
            
        } catch (\Exception $e) {
            Log::error("Error al verificar permisos: " . $e->getMessage());
            
            // En caso de error, permitimos acceso en producción pero registramos el problema
            if (config('app.env') === 'production' || config('app.debug')) {
                return $next($request);
            }
            
            return redirect('/home')->with('error', 'Error al verificar permisos. Contacte al administrador.');
        }
    }
}
