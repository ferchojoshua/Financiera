<?php

namespace App\Http\Controllers;

use App\db_supervisor_has_agent;
use App\db_summary;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DailyCloseController extends Controller
{
    /**
     * Constructor - Aplica middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role != 'supervisor' && Auth::user()->role != 'admin' && Auth::user()->role != 'superadmin') {
                return redirect('/home')->with('error', 'No tienes permisos para acceder a esta sección');
            }
            return $next($request);
        });
    }

    /**
     * Muestra la vista principal de cierre diario
     */
    public function index(Request $request)
    {
        return view('supervisor.daily_close');
    }

    /**
     * Filtra los resultados por agente y fecha
     */
    public function filter(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $agentId = $request->input('agent_id');
        
        return redirect()->route('supervisor.daily-close', ['date' => $date, 'agent_id' => $agentId]);
    }

    /**
     * Realiza el cierre diario para un agente específico
     */
    public function store(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'new_base' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);
        
        $agentId = $request->input('agent_id');
        $date = $request->input('date');
        $newBase = $request->input('new_base');
        $notes = $request->input('notes');
        
        // Verificar si ya existe un cierre para este agente en esta fecha
        $existingClose = DB::table('close_day')->where('id_agent', $agentId)
            ->whereDate('created_at', $date)
            ->first();
            
        if ($existingClose) {
            return redirect()->back()->with('error', 'Ya existe un cierre para este agente en esta fecha');
        }
        
        // Obtener datos para el cierre
        $agentRelation = db_supervisor_has_agent::where('id_user_agent', $agentId)->first();
        
        if (!$agentRelation) {
            return redirect()->back()->with('error', 'No se encontró la relación del agente con un supervisor');
        }
        
        $currentBase = $agentRelation->base;
        
        // Obtener cobros del día
        $collections = db_summary::where('id_agent', $agentId)
            ->whereDate('created_at', $date)
            ->sum('amount');
            
        // Obtener gastos del día
        $expenses = DB::table('bills')
            ->where('id_agent', $agentId)
            ->whereDate('created_at', $date)
            ->sum('amount');
            
        // Crear registro de cierre
        DB::table('close_day')->insert([
            'id_agent' => $agentId,
            'id_supervisor' => Auth::id(),
            'base_before' => $currentBase,
            'collections' => $collections,
            'expenses' => $expenses,
            'base_after' => $newBase,
            'notes' => $notes,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // Actualizar base del agente
        db_supervisor_has_agent::where('id_user_agent', $agentId)
            ->update([
                'base' => $newBase,
                'updated_at' => Carbon::now()
            ]);
            
        return redirect()->back()->with('success', 'Cierre diario realizado correctamente');
    }

    /**
     * Realiza el cierre diario para todos los agentes pendientes
     */
    public function storeAll(Request $request)
    {
        $request->validate([
            'close_date' => 'required|date',
            'global_notes' => 'nullable|string'
        ]);
        
        $date = $request->input('close_date');
        $notes = $request->input('global_notes');
        
        // Obtener agentes que no tienen cierre para la fecha seleccionada
        $agents = User::where('role', 'agent')
            ->whereNotExists(function ($query) use ($date) {
                $query->select(DB::raw(1))
                    ->from('close_day')
                    ->whereRaw('close_day.id_agent = users.id')
                    ->whereDate('close_day.created_at', $date);
            })
            ->get();
            
        $closedCount = 0;
        
        foreach ($agents as $agent) {
            $agentRelation = db_supervisor_has_agent::where('id_user_agent', $agent->id)->first();
            
            if (!$agentRelation) {
                continue;
            }
            
            $currentBase = $agentRelation->base;
            
            // Obtener cobros del día
            $collections = db_summary::where('id_agent', $agent->id)
                ->whereDate('created_at', $date)
                ->sum('amount');
                
            // Obtener gastos del día
            $expenses = DB::table('bills')
                ->where('id_agent', $agent->id)
                ->whereDate('created_at', $date)
                ->sum('amount');
                
            // Calcular nueva base
            $newBase = $currentBase + $collections - $expenses;
            
            // Crear registro de cierre
            DB::table('close_day')->insert([
                'id_agent' => $agent->id,
                'id_supervisor' => Auth::id(),
                'base_before' => $currentBase,
                'collections' => $collections,
                'expenses' => $expenses,
                'base_after' => $newBase,
                'notes' => $notes,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            // Actualizar base del agente
            db_supervisor_has_agent::where('id_user_agent', $agent->id)
                ->update([
                    'base' => $newBase,
                    'updated_at' => Carbon::now()
                ]);
                
            $closedCount++;
        }
        
        if ($closedCount > 0) {
            return redirect()->back()->with('success', "Se realizó el cierre diario para $closedCount agentes");
        } else {
            return redirect()->back()->with('info', 'No hay agentes pendientes de cierre para la fecha seleccionada');
        }
    }
} 