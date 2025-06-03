<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\User;
use App\Models\Credit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar listado de rutas
     */
    public function index(Request $request)
    {
        $query = Route::with('collector', 'supervisor');
        
        // Aplicar filtros si existen
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        if ($request->has('collector_id') && !empty($request->collector_id)) {
            $query->where('collector_id', $request->collector_id);
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        $routes = $query->orderBy('name')->paginate(10);
        $collectors = User::where(function($query) {
            $query->where('role', 'colector')
                  ->orWhere('role', 'cobrador');
        })->orderBy('name')->get();
        
        return view('routes.index', compact('routes', 'collectors'));
    }

    /**
     * Mostrar formulario para crear una nueva ruta
     */
    public function create()
    {
        $collectors = User::where(function($query) {
            $query->where('role', 'colector')
                  ->orWhere('role', 'cobrador');
        })->where('active_user', 1)->orderBy('name')->get();
        $supervisors = User::where('role', 'supervisor')->where('active_user', 1)->orderBy('name')->get();
        return view('routes.create', compact('collectors', 'supervisors'));
    }

    /**
     * Almacenar una nueva ruta
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:routes',
            'description' => 'nullable|string|max:255',
            'collector_id' => 'required|exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'zone' => 'nullable|string|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $route = new Route();
        $route->name = $request->name;
        $route->description = $request->description;
        $route->collector_id = $request->collector_id;
        $route->supervisor_id = $request->supervisor_id;
        $route->status = $request->status;
        $route->zone = $request->zone;
        $route->days = json_encode($request->days);
        $route->created_by = Auth::id();
        $route->save();

        return redirect()->route('routes.index')->with('success', 'Ruta creada correctamente');
    }

    /**
     * Mostrar detalle de una ruta específica con sus clientes
     */
    public function show($id)
    {
        $route = Route::with('collector', 'supervisor')->findOrFail($id);
        
        // Obtener los clientes que tienen préstamos activos en esta ruta
        $clients = User::whereHas('credits', function($query) use ($id) {
            $query->where('status', 'active')
                  ->where('route_id', $id);
        })->orderBy('name')->get();
        
        // Estadísticas de la ruta
        $stats = [
            'total_clients' => $clients->count(),
            'total_credits' => Credit::where('route_id', $id)->where('status', 'active')->count(),
            'total_amount' => Credit::where('route_id', $id)->where('status', 'active')->sum('amount'),
            'overdue_credits' => Credit::where('route_id', $id)->where('status', 'active')->where('is_overdue', true)->count(),
        ];
        
        return view('routes.show', compact('route', 'clients', 'stats'));
    }

    /**
     * Mostrar formulario para editar una ruta
     */
    public function edit($id)
    {
        $route = Route::findOrFail($id);
        $collectors = User::where(function($query) {
            $query->where('role', 'colector')
                  ->orWhere('role', 'cobrador');
        })->where('active_user', 1)->orderBy('name')->get();
        $supervisors = User::where('role', 'supervisor')->where('active_user', 1)->orderBy('name')->get();
        
        $selectedDays = json_decode($route->days);
        
        return view('routes.edit', compact('route', 'collectors', 'supervisors', 'selectedDays'));
    }

    /**
     * Actualizar una ruta existente
     */
    public function update(Request $request, $id)
    {
        $route = Route::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100|unique:routes,name,'.$id,
            'description' => 'nullable|string|max:255',
            'collector_id' => 'required|exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'zone' => 'nullable|string|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $route->name = $request->name;
        $route->description = $request->description;
        $route->collector_id = $request->collector_id;
        $route->supervisor_id = $request->supervisor_id;
        $route->status = $request->status;
        $route->zone = $request->zone;
        $route->days = json_encode($request->days);
        $route->updated_by = Auth::id();
        $route->save();

        return redirect()->route('routes.index')->with('success', 'Ruta actualizada correctamente');
    }

    /**
     * Asignar préstamos a una ruta
     */
    public function assign_credits($id)
    {
        $route = Route::findOrFail($id);
        
        // Préstamos ya asignados a esta ruta
        $assignedCredits = Credit::where('route_id', $id)->where('status', 'active')->with('user')->get();
        
        // Préstamos activos sin ruta asignada
        $unassignedCredits = Credit::whereNull('route_id')->where('status', 'active')->with('user')->get();
        
        return view('routes.assign_credits', compact('route', 'assignedCredits', 'unassignedCredits'));
    }

    /**
     * Guardar asignación de préstamos a una ruta
     */
    public function save_assign_credits(Request $request, $id)
    {
        $route = Route::findOrFail($id);
        
        $request->validate([
            'credit_ids' => 'nullable|array',
            'credit_ids.*' => 'exists:credits,id',
        ]);
        
        // Eliminar todas las asignaciones previas para esta ruta
        Credit::where('route_id', $id)->update(['route_id' => null]);
        
        // Asignar los préstamos seleccionados a esta ruta
        if ($request->has('credit_ids') && !empty($request->credit_ids)) {
            Credit::whereIn('id', $request->credit_ids)->update(['route_id' => $id]);
        }
        
        return redirect()->route('routes.show', $id)->with('success', 'Préstamos asignados correctamente a la ruta');
    }

    /**
     * Eliminar una ruta
     */
    public function destroy($id)
    {
        $route = Route::findOrFail($id);
        
        // Verificar si hay préstamos asociados
        $hasCredits = Credit::where('route_id', $id)->exists();
        
        if ($hasCredits) {
            return back()->with('error', 'No se puede eliminar la ruta porque tiene préstamos asociados');
        }
        
        $route->delete();
        
        return redirect()->route('routes.index')->with('success', 'Ruta eliminada correctamente');
    }
}
