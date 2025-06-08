<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            
            // Solo activar bypass en modo debug
            if (config('app.debug', false)) {
                $user->bypassAuthChecks = true;
                Log::info('Bypass de permisos activo para usuario: ' . $user->id . ' - ' . $user->name);
            }
        }
        
        return $next($request);
    }
}
