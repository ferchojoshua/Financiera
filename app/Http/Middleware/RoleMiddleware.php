<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Este middleware ha sido reemplazado por CheckRole.php
 * Se mantiene por compatibilidad pero no está registrado en el Kernel.
 * Por favor, use el middleware 'role' en su lugar.
 */
class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Si es superadmin o admin, tiene acceso a todo
        if ($user->role === 'superadmin' || $user->role === 'admin') {
            return $next($request);
        }

        // Verificar si el usuario tiene alguno de los roles permitidos
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
} 