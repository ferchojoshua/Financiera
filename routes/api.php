<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Ruta para obtener las rutas disponibles por sucursal
Route::get('/branches/{id}/routes', function($id) {
    try {
        // Registrar en los logs para diagnóstico
        \Log::info("API /branches/$id/routes llamada");
        
        // Verificar si la tabla route existe
        if (!Schema::hasTable('route')) {
            \Log::warning("API /branches/$id/routes: No se encontró la tabla 'route'");
            return response()->json([], 200);
        }
        
        // Buscar rutas activas
        $routes = DB::table('route')
            ->where('status', 'active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
            
        // Log del resultado para diagnóstico
        \Log::info("API /branches/$id/routes: Rutas encontradas: " . count($routes));
        
        return response()->json(array_values($routes), 200);
        
    } catch (\Exception $e) {
        \Log::error("API /branches/$id/routes: Error: " . $e->getMessage());
        return response()->json([], 500);
    }
});
