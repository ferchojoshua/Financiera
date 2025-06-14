<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\User;
use App\Models\Credit;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
        $query = Route::with('collector', 'supervisor', 'branch');
        
        // Obtener sucursal actual
        $currentBranchId = Session::get('current_branch_id');
        
        // Si hay una sucursal seleccionada, filtrar rutas por esa sucursal
        if ($currentBranchId) {
            $query->where('branch_id', $currentBranchId);
        }
        
        // Aplicar filtros si existen
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('collector_id') && !empty($request->collector_id)) {
            $query->where('collector_id', $request->collector_id);
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('branch_id') && !empty($request->branch_id)) {
            $query->where('branch_id', $request->branch_id);
        }
        
        // Obtener rutas después de aplicar filtros
        $routes = $query->orderBy('name')->paginate(10);
        
        // Contar total de rutas para el resumen (sin paginación)
        $query = Route::query();
        if ($currentBranchId) {
            $query->where('branch_id', $currentBranchId);
        }
        $routeCount = $query->count();
        
        $query = Route::query()->where('status', 'active');
        if ($currentBranchId) {
            $query->where('branch_id', $currentBranchId);
        }
        $activeRouteCount = $query->count();
        
        $query = Route::query()->where('status', 'inactive');
        if ($currentBranchId) {
            $query->where('branch_id', $currentBranchId);
        }
        $inactiveRouteCount = $query->count();
        
        // Obtener cobradores para filtro
        $collectors = User::where(function($query) {
            $query->where('role', 'colector')
                  ->orWhere('role', 'cobrador');
        })->orderBy('name')->get();
        
        // Obtener sucursales para filtro
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        
        // Calcular préstamos asignados totales
        $query = Credit::whereNotNull('route_id')->where('status', 'active');
        if ($currentBranchId) {
            $query->whereHas('route', function($q) use ($currentBranchId) {
                $q->where('branch_id', $currentBranchId);
            });
        }
        $totalAssignedCredits = $query->count();
        
        return view('routes.index', compact('routes', 'collectors', 'branches', 'routeCount', 'activeRouteCount', 'inactiveRouteCount', 'totalAssignedCredits', 'currentBranchId'));
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
        
        // Obtener sucursales activas
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        
        // Obtener sucursal actual
        $currentBranchId = Session::get('current_branch_id');
        
        return view('routes.create', compact('collectors', 'supervisors', 'branches', 'currentBranchId'));
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
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
            'zone' => 'nullable|string|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $route = new Route();
        $route->name = $request->name;
        $route->description = $request->description;
        $route->collector_id = $request->collector_id;
        $route->status = $request->status;
        $route->zone = $request->zone;
        $route->days = json_encode($request->days);
        $route->created_by = Auth::id();
        
        // Asignar la sucursal de la solicitud o la sucursal actual si no se proporciona
        if ($request->has('branch_id') && !empty($request->branch_id)) {
            $route->branch_id = $request->branch_id;
        } else {
            $route->branch_id = Session::get('current_branch_id');
        }
        
        $route->save();

        // Si se proporcionó un supervisor_id, crear o actualizar la relación en agent_has_supervisor
        if ($request->has('supervisor_id') && !empty($request->supervisor_id)) {
            DB::table('agent_has_supervisor')->updateOrInsert(
                [
                    'id_user_agent' => $request->collector_id,
                    'id_supervisor' => $request->supervisor_id
                ],
                [
                    'base' => 0.00,
                    'id_wallet' => DB::table('wallet')->first()->id ?? 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        return redirect()->route('routes.index')->with('success', 'Ruta creada correctamente');
    }

    /**
     * Mostrar detalle de una ruta específica con sus clientes
     */
    public function show($id)
    {
        $route = Route::with('collector', 'supervisor', 'branch')->findOrFail($id);
        
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
        
        // Obtener sucursales activas
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        
        $selectedDays = json_decode($route->days);
        
        return view('routes.edit', compact('route', 'collectors', 'supervisors', 'branches', 'selectedDays'));
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
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:active,inactive',
            'zone' => 'nullable|string|max:100',
            'days' => 'required|array|min:1',
            'days.*' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        $route->name = $request->name;
        $route->description = $request->description;
        $route->collector_id = $request->collector_id;
        $route->status = $request->status;
        $route->zone = $request->zone;
        $route->days = json_encode($request->days);
        $route->updated_by = Auth::id();
        
        if ($request->has('branch_id')) {
            $route->branch_id = $request->branch_id;
        }
        
        $route->save();

        // Actualizar la relación supervisor-agente
        if ($request->has('supervisor_id')) {
            if (!empty($request->supervisor_id)) {
                // Si hay supervisor, crear o actualizar la relación
                DB::table('agent_has_supervisor')->updateOrInsert(
                    [
                        'id_user_agent' => $request->collector_id,
                        'id_supervisor' => $request->supervisor_id
                    ],
                    [
                        'base' => DB::table('agent_has_supervisor')
                            ->where('id_user_agent', $request->collector_id)
                            ->value('base') ?? 0.00,
                        'id_wallet' => DB::table('wallet')->first()->id ?? 1,
                        'updated_at' => now()
                    ]
                );
            } else {
                // Si no hay supervisor, eliminar la relación existente
                DB::table('agent_has_supervisor')
                    ->where('id_user_agent', $request->collector_id)
                    ->delete();
            }
        }

        return redirect()->route('routes.index')->with('success', 'Ruta actualizada correctamente');
    }

    /**
     * Asignar préstamos a una ruta
     */
    public function assign_credits($id)
    {
        // Si el ID es 0, mostrar la lista de rutas disponibles
        if ($id == 0) {
            $routes = Route::where('status', 'active')->orderBy('name')->get();
            return view('routes.select_route_for_assignment', compact('routes'));
        }

        $route = Route::findOrFail($id);
        
        // Obtener créditos ya asignados a esta ruta
        $assignedCredits = Credit::where('route_id', $id)
                                ->where('status', 'active')
                                ->with('user')
                                ->get();
        
        // Obtener créditos disponibles para asignar (sin ruta asignada)
        $availableCredits = Credit::whereNull('route_id')
                                 ->where('status', 'active')
                                 ->with('user')
                                 ->get();
        
        return view('routes.assign_credits', compact('route', 'assignedCredits', 'availableCredits'));
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
            return redirect()->route('routes.index')
                ->with('error', 'No se puede eliminar la ruta porque tiene préstamos asociados');
        }
        
        $route->delete();
        
        return redirect()->route('routes.index')
                ->with('success', 'Ruta eliminada correctamente');
    }
    
    /**
     * Método para cambiar la sucursal actual
     */
    public function changeBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);
        
        Session::put('current_branch_id', $request->branch_id);
        
        return redirect()->back()->with('success', 'Sucursal seleccionada correctamente');
    }
}
