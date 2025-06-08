<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $user = $request->user();
        
        // Si el usuario tiene habilitado el bypass de permisos, permitir acceso
        if (method_exists($user, 'shouldBypassPermissionChecks') && $user->shouldBypassPermissionChecks()) {
            return $next($request);
        }
        
        // Si el usuario es superadmin o admin, permitir acceso a todo
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }
        
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $next($request);
        }
        
        // Verificación directa de atributos (compatibilidad)
        if ($user->role === 'superadmin' || $user->role === 'admin' || $user->level === 'admin') {
            return $next($request);
        }
        
        // Verificar roles específicos usando el método hasRole si está disponible
        foreach ($roles as $role) {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return $next($request);
            }
            
            // Verificación directa de atributos (compatibilidad)
            $userRole = isset($user->role) ? $user->role : $user->level;
            if ($userRole === $role) {
                return $next($request);
            }
        }
        
        // Registrar intento de acceso no autorizado
        $userRole = isset($user->role) ? $user->role : $user->level;
        Log::warning("Usuario ID {$user->id} con rol {$userRole} intentó acceder a una ruta protegida: {$request->path()}");
        
        // Redirigir con mensaje de error
        return redirect('/home')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
} 