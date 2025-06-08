<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UbicacionController extends Controller
{
    /**
     * Mostrar el mapa con las ubicaciones de los agentes
     */
    public function index()
    {
        // Obtener usuarios con rol de agente (colector)
        $agentes = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('roles.slug', '=', 'colector')
            ->select('users.id', 'users.name', 'users.email')
            ->get();
        
        return view('ubicaciones.mapa', compact('agentes'));
    }
    
    /**
     * Obtener la ubicación actual de un agente
     */
    public function obtenerUbicacion($id)
    {
        $ubicacion = DB::table('ubicaciones')
            ->where('user_id', $id)
            ->orderBy('ultima_actualizacion', 'desc')
            ->first();
            
        if (!$ubicacion) {
            return response()->json(['error' => 'No se encontró ubicación para este agente'], 404);
        }
        
        return response()->json([
            'id' => $ubicacion->id,
            'user_id' => $ubicacion->user_id,
            'latitud' => $ubicacion->latitud,
            'longitud' => $ubicacion->longitud,
            'direccion' => $ubicacion->direccion,
            'ultima_actualizacion' => $ubicacion->ultima_actualizacion
        ]);
    }
    
    /**
     * Obtener todas las ubicaciones de los agentes
     */
    public function obtenerTodasUbicaciones()
    {
        $ubicaciones = DB::table('ubicaciones as u')
            ->join('users as us', 'u.user_id', '=', 'us.id')
            ->join('user_roles as ur', 'us.id', '=', 'ur.user_id')
            ->join('roles as r', 'ur.role_id', '=', 'r.id')
            ->where('r.slug', '=', 'colector')
            ->select(
                'u.id', 
                'u.user_id', 
                'us.name as nombre_agente',
                'u.latitud', 
                'u.longitud', 
                'u.direccion', 
                'u.ultima_actualizacion'
            )
            ->orderBy('us.name')
            ->get();
            
        return response()->json($ubicaciones);
    }
    
    /**
     * Actualizar la ubicación de un agente
     */
    public function actualizarUbicacion(Request $request)
    {
        $request->validate([
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'direccion' => 'nullable|string|max:255',
        ]);
        
        // Por defecto, actualiza la ubicación del usuario autenticado
        $userId = Auth::id();
        
        // Si es admin, puede actualizar la ubicación de cualquier usuario
        if ($request->has('user_id') && Auth::user()->hasRole('admin')) {
            $userId = $request->input('user_id');
        }
        
        DB::table('ubicaciones')->insert([
            'user_id' => $userId,
            'latitud' => $request->input('latitud'),
            'longitud' => $request->input('longitud'),
            'direccion' => $request->input('direccion'),
            'ultima_actualizacion' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json(['message' => 'Ubicación actualizada correctamente']);
    }
    
    /**
     * Mostrar la vista de registro de actividades con mapa
     */
    public function registroActividades()
    {
        return view('ubicaciones.registro_actividades');
    }
}
