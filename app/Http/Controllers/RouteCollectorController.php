<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\RouteCollector;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RouteCollectorController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,superadmin,supervisor');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assignments = RouteCollector::with(['route', 'collector'])
            ->orderBy('assigned_date', 'desc')
            ->paginate(10);
            
        return view('routes.collectors.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Obtener todas las rutas activas
        $routes = Route::where('status', 'active')->orderBy('name')->get();
        
        // Obtener usuarios con rol de colector/cobrador
        $collectorRole = Role::where('slug', 'colector')->orWhere('slug', 'cobrador')->first();
        $collectors = [];
        
        if ($collectorRole) {
            $collectors = User::whereHas('roles', function($query) use ($collectorRole) {
                $query->where('role_id', $collectorRole->id);
            })->orderBy('name')->get();
        }
        
        return view('routes.collectors.create', compact('routes', 'collectors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'route_id' => 'required|exists:routes,id',
            'user_id' => 'required|exists:users,id',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:assigned_date',
            'notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('route.collectors.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Crear la asignación
        $assignment = new RouteCollector();
        $assignment->route_id = $request->route_id;
        $assignment->user_id = $request->user_id;
        $assignment->is_active = true;
        $assignment->assigned_date = $request->assigned_date;
        $assignment->end_date = $request->end_date;
        $assignment->notes = $request->notes;
        $assignment->assigned_by = Auth::id();
        $assignment->save();
        
        return redirect()->route('route.collectors.index')
            ->with('success', 'Cobrador asignado a la ruta correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $assignment = RouteCollector::with(['route', 'collector', 'assignedByUser'])->findOrFail($id);
        return view('routes.collectors.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $assignment = RouteCollector::findOrFail($id);
        
        // Obtener todas las rutas activas
        $routes = Route::where('status', 'active')->orderBy('name')->get();
        
        // Obtener usuarios con rol de colector/cobrador
        $collectorRole = Role::where('slug', 'colector')->orWhere('slug', 'cobrador')->first();
        $collectors = [];
        
        if ($collectorRole) {
            $collectors = User::whereHas('roles', function($query) use ($collectorRole) {
                $query->where('role_id', $collectorRole->id);
            })->orderBy('name')->get();
        }
        
        return view('routes.collectors.edit', compact('assignment', 'routes', 'collectors'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'route_id' => 'required|exists:routes,id',
            'user_id' => 'required|exists:users,id',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:assigned_date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('route.collectors.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
        
        // Actualizar la asignación
        $assignment = RouteCollector::findOrFail($id);
        $assignment->route_id = $request->route_id;
        $assignment->user_id = $request->user_id;
        $assignment->is_active = $request->has('is_active');
        $assignment->assigned_date = $request->assigned_date;
        $assignment->end_date = $request->end_date;
        $assignment->notes = $request->notes;
        $assignment->save();
        
        return redirect()->route('route.collectors.index')
            ->with('success', 'Asignación actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assignment = RouteCollector::findOrFail($id);
        $assignment->delete();
        
        return redirect()->route('route.collectors.index')
            ->with('success', 'Asignación eliminada correctamente');
    }
}
