<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashCategory;
use App\Models\CashRegister;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Wallet;
use Carbon\Carbon;

class SupervisorController extends Controller
{
    public function agent()
    {
        // Obtenemos los datos necesarios para la vista
        $data = \App\db_supervisor_has_agent::where('agent_has_supervisor.id_supervisor', \Illuminate\Support\Facades\Auth::id())
            ->join('users','id_user_agent','=','users.id')
            ->join('wallet','agent_has_supervisor.id_wallet','=','wallet.id')
            ->select(
                'users.*',
                'wallet.name as wallet_name',
                'agent_has_supervisor.base as base_total'
            )
            ->get();
        
        $data = array(
            'clients' => $data,
            'today' => \Carbon\Carbon::now()->toDateString(),
        );
        
        return view('supervisor_agent.index', $data);
    }

    public function close()
    {
        $date = request()->input('date', date('Y-m-d'));
        $agentFilter = request()->input('agent_id');
        
        $agents = User::where('role', 'agent')
            ->when($agentFilter, function($query) use ($agentFilter) {
                return $query->where('id', $agentFilter);
            })
            ->orderBy('name')
            ->get();
            
        foreach ($agents as $agent) {
            // Verificar si existe un cierre para este agente en esta fecha
            $closeDayExists = DB::table('close_day')
                ->where('id_agent', $agent->id)
                ->whereDate('created_at', $date)
                ->exists();
                
            $agent->closeDayExists = $closeDayExists;
            
            // Obtener base actual del agente
            $agentRelation = DB::table('agent_has_supervisor')
                ->where('id_user_agent', $agent->id)
                ->first();
                
            $agent->base = $agentRelation ? $agentRelation->base : 0;
            
            // Obtener recaudos del día
            $summary = DB::table('summary')
                ->where('id_agent', $agent->id)
                ->whereDate('created_at', $date)
                ->sum('amount');
                
            $agent->summary = $summary;
            
            // Obtener gastos del día
            $bills = DB::table('bills')
                ->where('id_agent', $agent->id)
                ->whereDate('created_at', $date)
                ->sum('amount');
                
            $agent->bills = $bills;
            
            // Calcular balance
            $agent->balance = $agent->base + $summary - $bills;
        }
        
        return view('supervisor.close', [
            'agents' => $agents,
            'date' => $date,
            'filters' => [
                'agent_id' => $agentFilter,
                'date' => $date
            ]
        ]);
    }

    public function client()
    {
        return view('supervisor.client');
    }

    public function tracker()
    {
        return view('supervisor.tracker');
    }

    public function reviewCreate()
    {
        return view('supervisor.review.create');
    }

    public function statistics()
    {
        return view('supervisor.statistics');
    }

    public function cash()
    {
        try {
            $categories = CashCategory::orderBy('name')->get();
            $movimientos = CashRegister::with('category')
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
            
            // Calcular el saldo total desde la tabla cash_register si existe
            if (Schema::hasTable('cash_register')) {
                $saldoTotal = CashRegister::where('type', 'ingreso')->sum('amount') - 
                             CashRegister::where('type', 'egreso')->sum('amount');
                
                $ingresosHoy = CashRegister::where('type', 'ingreso')
                              ->whereDate('created_at', date('Y-m-d'))
                              ->sum('amount');
                
                $egresosHoy = CashRegister::where('type', 'egreso')
                             ->whereDate('created_at', date('Y-m-d'))
                             ->sum('amount');
            } else {
                // Usar la tabla summary como alternativa
                $saldoTotal = \App\db_summary::sum('amount');
                
                $ingresosHoy = \App\db_summary::whereDate('created_at', date('Y-m-d'))
                              ->sum('amount');
                
                $egresosHoy = 0; // La tabla summary no tiene egresos separados
            }
            
            return view('supervisor.cash', compact('categories', 'movimientos', 'saldoTotal', 'ingresosHoy', 'egresosHoy'));
        } catch (\Exception $e) {
            return view('supervisor.cash')->with('error', 'Error al cargar los datos: ' . $e->getMessage());
        }
    }

    public function billCreate()
    {
        return view('supervisor.bill.create');
    }
    
    public function cashIncome(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string|max:255',
                'type' => 'required|in:ingreso',
                'category_id' => 'nullable|exists:cash_categories,id',
            ]);
            
            CashRegister::create([
                'type' => 'ingreso',
                'amount' => $request->amount,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'id_user_agent' => auth()->id(),
            ]);
            
            return redirect()->route('supervisor.cash')->with('success', 'Ingreso registrado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al registrar el ingreso: ' . $e->getMessage())->withInput();
        }
    }
    
    public function cashExpense(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string|max:255',
                'type' => 'required|in:egreso',
                'category_id' => 'nullable|exists:cash_categories,id',
            ]);
            
            CashRegister::create([
                'type' => 'egreso',
                'amount' => $request->amount,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'id_user_agent' => auth()->id(),
            ]);
            
            return redirect()->route('supervisor.cash')->with('success', 'Egreso registrado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al registrar el egreso: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the agent's base.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function agentEdit($id)
    {
        $data = \App\User::where('users.id', $id)
            ->join('agent_has_supervisor', 'users.id', '=', 'agent_has_supervisor.id_user_agent')
            ->join('wallet', 'agent_has_supervisor.id_wallet', '=', 'wallet.id')
            ->select(
                'users.name',
                'users.last_name',
                'users.country',
                'users.address',
                'wallet.name as wallet_name',
                'users.id',
                'agent_has_supervisor.base as base_current'
            )
            ->first();

        return view('supervisor_agent.edit', $data);
    }

    /**
     * Update the agent's base.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function agentUpdate(Request $request, $id)
    {
        $base = $request->base_number;
        if(!isset($base)) {
            return redirect()->back()->with('error', 'Base vacía');
        }
        
        $base_current = \App\db_supervisor_has_agent::where('id_user_agent', $id)
            ->where('id_supervisor', \Illuminate\Support\Facades\Auth::id())->first()->base;
        
        $base = $base_current + $base;
        
        \App\db_supervisor_has_agent::where('id_user_agent', $id)
            ->where('id_supervisor', \Illuminate\Support\Facades\Auth::id())
            ->update(['base' => $base]);

        return redirect('supervisor/agent')->with('success', 'Base actualizada correctamente');
    }

    /**
     * Display the main supervisor dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $agents = User::where('level', 'agent')->get();
        $routes = \App\Models\Route::all();
        $credits = \App\Models\Credit::all();
        $summary = DB::table('summary')->get();
        
        // Estadísticas básicas
        $totalAgents = $agents->count();
        $totalRoutes = $routes->count();
        $totalCredits = $credits->count();
        $totalPayments = $summary->count();
        
        // Montos totales
        $totalCreditAmount = $credits->sum('amount');
        $totalPaymentAmount = $summary->sum('amount');
        
        return view('supervisor.index', compact(
            'totalAgents',
            'totalRoutes',
            'totalCredits',
            'totalPayments',
            'totalCreditAmount',
            'totalPaymentAmount',
            'agents',
            'routes'
        ));
    }

    public function storeClose(Request $request)
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
        $agentRelation = DB::table('agent_has_supervisor')->where('id_user_agent', $agentId)->first();
        
        if (!$agentRelation) {
            return redirect()->back()->with('error', 'No se encontró la relación del agente con un supervisor');
        }
        
        $currentBase = $agentRelation->base;
        
        // Obtener cobros del día
        $collections = DB::table('summary')->where('id_agent', $agentId)
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
        DB::table('agent_has_supervisor')->where('id_user_agent', $agentId)
            ->update([
                'base' => $newBase,
                'updated_at' => Carbon::now()
            ]);
            
        return redirect()->back()->with('success', 'Cierre diario realizado correctamente');
    }
    
    public function storeAllClose(Request $request)
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
            $agentRelation = DB::table('agent_has_supervisor')->where('id_user_agent', $agent->id)->first();
            
            if (!$agentRelation) {
                continue;
            }
            
            $currentBase = $agentRelation->base;
            
            // Obtener cobros del día
            $collections = DB::table('summary')->where('id_agent', $agent->id)
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
            DB::table('agent_has_supervisor')->where('id_user_agent', $agent->id)
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