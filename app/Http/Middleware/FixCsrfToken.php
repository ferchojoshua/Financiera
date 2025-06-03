<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FixCsrfToken
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
        // Verificar si hay un token en la sesión, si no, regenerarlo
        if (!$request->session()->has('_token')) {
            $request->session()->regenerateToken();
        }
        
        // Si es una solicitud de login, asegurar que tiene el token correcto
        if ($request->is('login') && $request->isMethod('post')) {
            // Log de debugging
            Log::info('Login request detected', [
                'has_token' => $request->has('_token'),
                'has_csrf' => $request->header('X-CSRF-TOKEN') ? 'yes' : 'no',
                'session_token' => $request->session()->token()
            ]);
            
            // Comprobar si el token no está presente en el formulario
            if (!$request->has('_token')) {
                // Añadir el token manualmente
                $request->request->add(['_token' => $request->session()->token()]);
            }
        }
        
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Registrar el error
            Log::error('CSRF Token Mismatch: ' . $e->getMessage(), [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'is_ajax' => $request->ajax(),
                'request_token' => $request->input('_token'),
                'session_token' => $request->session()->token()
            ]);
            
            // Regenerar el token CSRF
            $request->session()->regenerateToken();
            
            // Si es una petición AJAX, devolver respuesta JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Su sesión ha expirado. Por favor, recargue la página e intente nuevamente.',
                    'reload' => true
                ], 419);
            }
            
            // Para login específicamente, redirigir a la página de login
            if ($request->is('login')) {
                return redirect()->route('login')
                    ->with('error', 'Su sesión ha expirado. Por favor, intente nuevamente.');
            }
            
            // Para peticiones normales, redireccionar a la página anterior con mensaje
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Su sesión ha expirado. Por favor, intente nuevamente.');
        } catch (HttpException $e) {
            // Capturar excepciones HTTP incluyendo CSRF
            if ($e->getMessage() === 'CSRF token mismatch.') {
                // Regenerar el token CSRF
                $request->session()->regenerateToken();
                
                // Si es una petición AJAX, devolver respuesta JSON
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Su sesión ha expirado. Por favor, recargue la página e intente nuevamente.',
                        'reload' => true
                    ], 419);
                }
                
                // Para login específicamente, redirigir a la página de login
                if ($request->is('login')) {
                    return redirect()->route('login')
                        ->with('error', 'Su sesión ha expirado. Por favor, intente nuevamente.');
                }
                
                // Para peticiones normales, redireccionar
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                    ->with('error', 'Su sesión ha expirado. Por favor, intente nuevamente.');
            }
            
            throw $e; // Re-lanzar la excepción si no es CSRF
        }
    }
} 