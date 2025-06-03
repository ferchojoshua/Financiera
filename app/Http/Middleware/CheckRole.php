<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        // Si el usuario es superadmin, permitir acceso a todo
        if ($request->user()->role === 'superadmin' || $request->user()->role === 'admin') {
            return $next($request);
        }
        
        // Verificar por 'level' (compatibilidad con código antiguo) o 'role' (nuevo sistema)
        $userRole = $request->user()->role ?? $request->user()->level;
        
        foreach ($roles as $role) {
            if ($userRole === $role) {
                return $next($request);
            }
        }

        return redirect('/home')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
} 