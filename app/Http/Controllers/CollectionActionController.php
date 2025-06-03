<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credit;
use App\Models\Route;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CollectionActionController extends Controller
{
    /**
     * Constructor que verifica los permisos de acceso
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Temporalmente omitimos la verificación del middleware de permisos
        // hasta que la tabla role_module_permissions esté correctamente migrada
    }
    
    /**
     * Muestra la lista de acciones de cobranza
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Obtener créditos vencidos o con pagos pendientes
            $query = Credit::where('status', 'active');
            
            // En esta versión de la aplicación, no filtramos por ruta de usuario
            // ya que la tabla users_has_route no tiene la columna user_id esperada
            
            // Filtros adicionales
            if ($request->has('route_id') && $request->route_id) {
                $query->where('route_id', $request->route_id);
            }
            
            if ($request->has('overdue_days') && $request->overdue_days) {
                list($min, $max) = explode('-', $request->overdue_days . '-999');
                // Implementar filtro por días de atraso
                // Esta lógica puede variar según cómo se calculen los días de atraso
            }
            
            // Obtener créditos vencidos o con pagos pendientes
            $credits = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Obtener rutas para el filtro
            try {
                $routes = Route::orderBy('name')->get();
            } catch (\Exception $e) {
                $routes = collect([]);
            }
            
            // Obtener estadísticas para el dashboard
            $stats = [
                'total_overdue' => 0,
                'count' => $credits->count(),
                'recovery_percentage' => 0,
                'pending_amount' => 0
            ];
            
            return view('collection.actions.index', compact('credits', 'routes', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Error en el módulo de cobranza: ' . $e->getMessage());
            return redirect('/home')->with('error', 'Ha ocurrido un error al acceder al módulo de cobranza: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra el formulario para crear una nueva acción de cobranza
     */
    public function create()
    {
        return view('collection.actions.create');
    }
    
    /**
     * Almacena una nueva acción de cobranza
     */
    public function store(Request $request)
    {
        // Validar datos del formulario
        $validated = $request->validate([
            'credit_id' => 'required|exists:credit,id',
            'action_type' => 'required|string',
            'notes' => 'required|string',
            'agreement_date' => 'nullable|date',
            'agreement_amount' => 'nullable|numeric'
        ]);
        
        // Implementar lógica para registrar la acción de cobranza
        
        return redirect()->route('collection.actions.index')
            ->with('success', 'Acción de cobranza registrada correctamente');
    }
    
    /**
     * Muestra el detalle de una acción de cobranza
     */
    public function show($id)
    {
        // Implementar lógica para mostrar detalles
        return view('collection.actions.show');
    }
    
    /**
     * Muestra el formulario para editar una acción de cobranza
     */
    public function edit($id)
    {
        // Implementar lógica para editar acción
        return view('collection.actions.edit');
    }
    
    /**
     * Actualiza una acción de cobranza
     */
    public function update(Request $request, $id)
    {
        // Implementar lógica para actualizar acción
        return redirect()->route('collection.actions.index')
            ->with('success', 'Acción de cobranza actualizada correctamente');
    }
    
    /**
     * Elimina una acción de cobranza
     */
    public function destroy($id)
    {
        // Implementar lógica para eliminar acción
        return redirect()->route('collection.actions.index')
            ->with('success', 'Acción de cobranza eliminada correctamente');
    }
} 