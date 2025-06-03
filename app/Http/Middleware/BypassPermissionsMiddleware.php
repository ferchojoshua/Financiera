<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BypassPermissionsMiddleware
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
        if (Auth::check()) {
            $user = Auth::user();
            
            // Guardamos el rol original solo si no se ha hecho antes
            if (!isset($user->originalRole)) {
                $user->originalRole = $user->role;
                $user->originalLevel = $user->level;
            }
            
            // Activamos el bypass de permisos
            $user->bypassAuthChecks = true;
            
            // No modificamos directamente el rol para evitar problemas con las consultas
            // Solo habilitamos el bypass que usará los métodos de comprobación
        }
        
        return $next($request);
    }
}
