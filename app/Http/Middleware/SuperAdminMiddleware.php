<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\\Http\\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario es superadmin por level=admin y role=superadmin
        if (!$request->user() || 
            !($request->user()->level === 'admin' && $request->user()->role === 'superadmin')) {
            
            // Si la ruta actual está relacionada con cobranza, dirigir directamente ahí
            if (strpos($request->path(), 'collection') !== false) {
                return redirect('/collection/actions')->with('error', 'No tienes permisos de Super Administrador para acceder a esta sección.');
            }
            
            return redirect('/home')->with('error', 'No tienes permisos de Super Administrador para acceder a esta sección.');
        }

        return $next($request);
    }
} 