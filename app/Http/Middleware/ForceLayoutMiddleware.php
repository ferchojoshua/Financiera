<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class ForceLayoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Configurar variables globales para todas las vistas
        // Esto forzará el uso del layout master para la ruta de clientes
        if ($request->is('clients') || $request->is('clients/*')) {
            View::share('_layout', 'layouts.master');
        }
        
        return $next($request);
    }
} 