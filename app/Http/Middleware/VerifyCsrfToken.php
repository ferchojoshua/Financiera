<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Excluir temporalmente para diagnóstico
        'login',
        'config/permisos/update'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Verificar si hay un token en la sesión
        if (!$request->session()->has('_token')) {
            // Regenerar el token si no existe
            $request->session()->regenerateToken();
        }

        return parent::handle($request, $next);
    }
}
