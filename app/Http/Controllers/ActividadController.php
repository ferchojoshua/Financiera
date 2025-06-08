<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    /**
     * Mostrar lista de actividades
     */
    public function index()
    {
        $actividades = Actividad::with(['usuario', 'cliente'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('actividades.index', compact('actividades'));
    }
    
    /**
     * Mostrar formulario para crear nueva actividad
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('actividades.create', compact('clientes'));
    }
    
    /**
     * Almacenar una nueva actividad
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_actividad' => 'required|string|max:50',
            'descripcion' => 'required|string',
            'resultado' => 'required|string|max:50',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'direccion' => 'nullable|string|max:255',
        ]);
        
        $actividad = new Actividad();
        $actividad->user_id = Auth::id();
        $actividad->cliente_id = $request->cliente_id;
        $actividad->tipo_actividad = $request->tipo_actividad;
        $actividad->descripcion = $request->descripcion;
        $actividad->resultado = $request->resultado;
        
        if ($request->has('latitud') && $request->has('longitud')) {
            $actividad->latitud = $request->latitud;
            $actividad->longitud = $request->longitud;
            $actividad->direccion = $request->direccion;
        }
        
        $actividad->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Actividad registrada correctamente',
                'actividad' => $actividad
            ]);
        }
        
        return redirect()->route('actividades.index')
            ->with('success', 'Actividad registrada correctamente');
    }
    
    /**
     * Mostrar los detalles de una actividad
     */
    public function show($id)
    {
        $actividad = Actividad::with(['usuario', 'cliente'])->findOrFail($id);
        return view('actividades.show', compact('actividad'));
    }
    
    /**
     * Mostrar el formulario para editar actividad
     */
    public function edit($id)
    {
        $actividad = Actividad::findOrFail($id);
        $clientes = Cliente::orderBy('nombre')->get();
        
        // Solo permitir editar actividades propias (excepto admin)
        if ($actividad->user_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return redirect()->route('actividades.index')
                ->with('error', 'No tiene permiso para editar esta actividad');
        }
        
        return view('actividades.edit', compact('actividad', 'clientes'));
    }
    
    /**
     * Actualizar una actividad
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_actividad' => 'required|string|max:50',
            'descripcion' => 'required|string',
            'resultado' => 'required|string|max:50',
        ]);
        
        $actividad = Actividad::findOrFail($id);
        
        // Solo permitir editar actividades propias (excepto admin)
        if ($actividad->user_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return redirect()->route('actividades.index')
                ->with('error', 'No tiene permiso para editar esta actividad');
        }
        
        $actividad->cliente_id = $request->cliente_id;
        $actividad->tipo_actividad = $request->tipo_actividad;
        $actividad->descripcion = $request->descripcion;
        $actividad->resultado = $request->resultado;
        $actividad->save();
        
        return redirect()->route('actividades.index')
            ->with('success', 'Actividad actualizada correctamente');
    }
    
    /**
     * Eliminar una actividad
     */
    public function destroy($id)
    {
        $actividad = Actividad::findOrFail($id);
        
        // Solo permitir eliminar actividades propias (excepto admin)
        if ($actividad->user_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            return redirect()->route('actividades.index')
                ->with('error', 'No tiene permiso para eliminar esta actividad');
        }
        
        $actividad->delete();
        
        return redirect()->route('actividades.index')
            ->with('success', 'Actividad eliminada correctamente');
    }
    
    /**
     * API para obtener actividades de un cliente
     */
    public function actividadesCliente($clienteId)
    {
        $actividades = Actividad::with('usuario')
            ->where('cliente_id', $clienteId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($actividades);
    }
}
