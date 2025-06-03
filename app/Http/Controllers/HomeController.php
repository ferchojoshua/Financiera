<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Credit;
use App\Models\Payment;
use App\Models\Summary;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Variables básicas para la vista
        $data = [
            'close_day' => false,
            'totalRecuperado' => 0,
            'totalMorosos' => 0,
            'porcentajeRecuperacion' => '0%',
            'clientesActivos' => 0,
            'ultimosPagos' => [],
            'morososRecientes' => [],
            'totalDesembolsado' => 0,
            'comparativaRecuperacion' => 0,
            'base_agent' => 0,
            'total_bill' => 0,
            'total_summary' => 0
        ];

        // Verificar si se ha cerrado el día (para agentes)
        if ($user->role == 'agent') {
            $data['close_day'] = $this->checkDayClosed($user->id);
            
            // Obtener información de base y resumen para agentes
            $base = Wallet::where('id_agent', $user->id)
                ->first();
            
            if ($base) {
                $data['base_agent'] = $base->base;
                
                // Calcular gastos del día
                $data['total_bill'] = Bill::where('id_agent', $user->id)
                    ->whereDate('created_at', Carbon::now()->toDateString())
                    ->sum('amount');
                
                // Calcular cobros del día
                $data['total_summary'] = Summary::where('id_agent', $user->id)
                    ->whereDate('created_at', Carbon::now()->toDateString())
                    ->sum('amount');
            }
            
            $agentStats = $this->getAgentStats($user->id);
            $data = array_merge($data, $agentStats);
        } 
        // Estadísticas para supervisores
        elseif ($user->role == 'supervisor') {
            $supervisorStats = $this->getSupervisorStats($user->id);
            $data = array_merge($data, $supervisorStats);
        } 
        // Estadísticas para administradores
        elseif ($user->level == 'admin' || $user->level == 'superadmin') {
            $adminStats = $this->getAdminStats();
            $data = array_merge($data, $adminStats);
        }
        
        return view('home', $data);
    }

    /**
     * Verifica si el agente ha cerrado el día
     */
    private function checkDayClosed($agentId)
    {
        $today = Carbon::now()->toDateString();
        $wallet = Wallet::where('id_agent', $agentId)->first();
        
        if ($wallet && $wallet->last_close) {
            return Carbon::parse($wallet->last_close)->toDateString() == $today;
        }
        
        return false;
    }

    private function getAdminStats()
    {
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Total recuperado del mes
        $totalRecuperado = Payment::whereBetween('created_at', [$startOfMonth, $today])
            ->sum('amount');

        // Total desembolsado del mes
        $totalDesembolsado = Credit::whereBetween('created_at', [$startOfMonth, $today])
            ->sum('amount_neto');
            
        // Comparativa recuperación vs desembolso
        $comparativaRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2)
            : 0;

        // Total de morosos
        $creditosMorosos = Credit::where('status', 'inprogress')
            ->whereRaw('DATEDIFF(NOW(), updated_at) > 7')
            ->get();
            
        $totalMorosos = count($creditosMorosos);

        // Porcentaje de recuperación
        $porcentajeRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2) . '%'
            : '0%';

        // Clientes activos
        $clientesActivos = Credit::where('status', 'inprogress')
            ->count();

        // Últimos pagos - Corregido
        $ultimosPagos = DB::table('summary')
            ->select('summary.amount as monto', 'summary.created_at as fecha', 'users.name as cliente')
            ->join('credit', 'summary.id_credit', '=', 'credit.id')
            ->join('users', 'credit.id_user', '=', 'users.id')
            ->orderBy('summary.created_at', 'desc')
            ->limit(5)
            ->get();

        if (count($ultimosPagos) == 0) {
            // Si no hay resultados, dejamos un array vacío
            $ultimosPagos = [];
        }
            
        // Morosos recientes
        $morososRecientes = [];
        foreach ($creditosMorosos as $credito) {
            $cliente = User::find($credito->id_user);
            if ($cliente) {
                $diasAtraso = Carbon::parse($credito->updated_at)->diffInDays(Carbon::now());
                $morososRecientes[] = (object)[
                    'cliente' => $cliente->name,
                    'dias_atraso' => $diasAtraso,
                    'monto_pendiente' => $credito->amount_neto - Payment::where('id_credit', $credito->id)->sum('amount'),
                    'id' => $credito->id
                ];
            }
            
            if (count($morososRecientes) >= 5) break;
        }

        return [
            'totalRecuperado' => $totalRecuperado,
            'totalDesembolsado' => $totalDesembolsado,
            'comparativaRecuperacion' => $comparativaRecuperacion,
            'totalMorosos' => $totalMorosos,
            'porcentajeRecuperacion' => $porcentajeRecuperacion,
            'clientesActivos' => $clientesActivos,
            'ultimosPagos' => $ultimosPagos,
            'morososRecientes' => $morososRecientes
        ];
    }

    private function getSupervisorStats($userId)
    {
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Obtener agentes supervisados
        $agentIds = User::where('supervisor_id', $userId)->pluck('id')->toArray();

        // Total recuperado del mes por agentes supervisados
        $totalRecuperado = Payment::whereIn('id_agent', $agentIds)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('amount');

        // Total desembolsado del mes
        $totalDesembolsado = Credit::whereIn('id_agent', $agentIds)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('amount_neto');
            
        // Comparativa recuperación vs desembolso
        $comparativaRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2)
            : 0;

        // Total de morosos
        $creditosMorosos = Credit::whereIn('id_agent', $agentIds)
            ->where('status', 'inprogress')
            ->whereRaw('DATEDIFF(NOW(), updated_at) > 7')
            ->get();
            
        $totalMorosos = count($creditosMorosos);

        // Porcentaje de recuperación
        $porcentajeRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2) . '%'
            : '0%';

        // Clientes activos
        $clientesActivos = Credit::whereIn('id_agent', $agentIds)
            ->where('status', 'inprogress')
            ->count();

        // Últimos pagos - Corregido
        $ultimosPagos = DB::table('summary')
            ->select('summary.amount as monto', 'summary.created_at as fecha', 'users.name as cliente')
            ->join('credit', 'summary.id_credit', '=', 'credit.id')
            ->join('users', 'credit.id_user', '=', 'users.id')
            ->whereIn('summary.id_agent', $agentIds)
            ->orderBy('summary.created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Morosos recientes
        $morososRecientes = [];
        foreach ($creditosMorosos as $credito) {
            $cliente = User::find($credito->id_user);
            if ($cliente) {
                $diasAtraso = Carbon::parse($credito->updated_at)->diffInDays(Carbon::now());
                $morososRecientes[] = (object)[
                    'cliente' => $cliente->name,
                    'dias_atraso' => $diasAtraso,
                    'monto_pendiente' => $credito->amount_neto - Payment::where('id_credit', $credito->id)->sum('amount'),
                    'id' => $credito->id
                ];
            }
            
            if (count($morososRecientes) >= 5) break;
        }

        return [
            'totalRecuperado' => $totalRecuperado,
            'totalDesembolsado' => $totalDesembolsado,
            'comparativaRecuperacion' => $comparativaRecuperacion,
            'totalMorosos' => $totalMorosos,
            'porcentajeRecuperacion' => $porcentajeRecuperacion,
            'clientesActivos' => $clientesActivos,
            'ultimosPagos' => $ultimosPagos,
            'morososRecientes' => $morososRecientes
        ];
    }

    private function getAgentStats($userId)
    {
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Total recuperado del mes
        $totalRecuperado = Payment::where('id_agent', $userId)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('amount');

        // Total desembolsado del mes
        $totalDesembolsado = Credit::where('id_agent', $userId)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('amount_neto');
            
        // Comparativa recuperación vs desembolso
        $comparativaRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2)
            : 0;

        // Total de morosos
        $creditosMorosos = Credit::where('id_agent', $userId)
            ->where('status', 'inprogress')
            ->whereRaw('DATEDIFF(NOW(), updated_at) > 7')
            ->get();
            
        $totalMorosos = count($creditosMorosos);

        // Porcentaje de recuperación
        $porcentajeRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2) . '%'
            : '0%';

        // Clientes activos
        $clientesActivos = Credit::where('id_agent', $userId)
            ->where('status', 'inprogress')
            ->count();

        // Últimos pagos - Corregido
        $ultimosPagos = DB::table('summary')
            ->select('summary.amount as monto', 'summary.created_at as fecha', 'users.name as cliente')
            ->join('credit', 'summary.id_credit', '=', 'credit.id')
            ->join('users', 'credit.id_user', '=', 'users.id')
            ->where('summary.id_agent', $userId)
            ->orderBy('summary.created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Morosos recientes
        $morososRecientes = [];
        foreach ($creditosMorosos as $credito) {
            $cliente = User::find($credito->id_user);
            if ($cliente) {
                $diasAtraso = Carbon::parse($credito->updated_at)->diffInDays(Carbon::now());
                $morososRecientes[] = (object)[
                    'cliente' => $cliente->name,
                    'dias_atraso' => $diasAtraso,
                    'monto_pendiente' => $credito->amount_neto - Payment::where('id_credit', $credito->id)->sum('amount'),
                    'id' => $credito->id
                ];
            }
            
            if (count($morososRecientes) >= 5) break;
        }

        return [
            'totalRecuperado' => $totalRecuperado,
            'totalDesembolsado' => $totalDesembolsado,
            'comparativaRecuperacion' => $comparativaRecuperacion,
            'totalMorosos' => $totalMorosos,
            'porcentajeRecuperacion' => $porcentajeRecuperacion,
            'clientesActivos' => $clientesActivos,
            'ultimosPagos' => $ultimosPagos,
            'morososRecientes' => $morososRecientes
        ];
    }
}
