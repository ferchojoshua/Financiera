<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo proceder si hay un usuario autenticado
        if (Auth::check()) {
            // Añadir propiedad temporal al usuario para forzar acceso
            $user = Auth::user();
            
            // Añadir método para siempre devolver true en isSuperAdmin
            if (!method_exists($user, 'forceAdmin')) {
                $user->forceAdmin = true;
                
                // Agregar métodos dinámicos para bypass de verificaciones
                $user->isAdmin = function() {
                    return true;
                };
                
                $user->isSuperAdmin = function() {
                    return true;
                };
                
                $user->hasModuleAccess = function($module) {
                    return true;
                };
                
                $user->hasPermission = function($permission) {
                    return true;
                };
            }
        }
        
        return $next($request);
    }
}
