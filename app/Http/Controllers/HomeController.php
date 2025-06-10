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
        
        // Verificar el rol del usuario y obtener las estadísticas correspondientes
        if ($user->role === 'supervisor') {
            $stats = $this->getSupervisorStats($user->id);
        } elseif ($user->role === 'agent') {
            $stats = $this->getAgentStats($user->id);
        } else {
            $stats = $this->getAdminStats();
        }

        // Verificar si se ha cerrado el día (para agentes)
        $close_day = false;
        if ($user->role === 'agent') {
            $today = Carbon::now()->format('Y-m-d');
            $close_day = DB::table('close_day')
                ->where('id_agent', $user->id)
                ->whereDate('created_at', $today)
                ->exists();
        }

        // Combinar las estadísticas con la variable de cierre del día
        return view('home', array_merge($stats, ['close_day' => $close_day]));
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
        $totalRecuperado = DB::table('summary')->whereBetween('created_at', [$startOfMonth, $today])->sum('amount');

        // Total desembolsado del mes
        $totalDesembolsado = Credit::whereBetween('created_at', [$startOfMonth, $today])->sum('amount_neto');
            
        // Total de morosos
        $creditosMorosos = Credit::where('status', 'overdue')->get();
        $totalMorosos = $creditosMorosos->count();

        // Porcentaje de recuperación
        $porcentajeRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2)
            : 0;

        // Clientes activos
        $clientesActivos = Credit::where('status', 'inprogress')->distinct('client_id')->count();

        // Últimos pagos
        $ultimosPagos = DB::table('summary')
            ->select('summary.amount as monto', 'summary.created_at as fecha', 'clients.name as cliente_nombre', 'clients.last_name as cliente_apellido')
            ->join('credit', 'summary.id_credit', '=', 'credit.id')
            ->join('clients', 'credit.client_id', '=', 'clients.id')
            ->orderBy('summary.created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Morosos recientes
        $morososRecientes = [];
        foreach ($creditosMorosos->take(5) as $credito) {
            $cliente = $credito->client;
            if ($cliente) {
                // Usar updated_at si overdue_date no existe o es nulo
                $fechaAtraso = $credito->overdue_date ?? $credito->updated_at;
                $diasAtraso = Carbon::parse($fechaAtraso)->diffInDays(Carbon::now());
                $morososRecientes[] = (object)[
                    'cliente' => $cliente->name . ' ' . $cliente->last_name,
                    'dias_atraso' => $diasAtraso,
                    'monto_pendiente' => $credito->balance,
                    'id' => $credito->id
                ];
            }
        }

        return [
            'totalRecuperado' => $totalRecuperado,
            'totalDesembolsado' => $totalDesembolsado,
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

        // Obtener los IDs de los agentes supervisados
        $agentIds = DB::table('agent_has_supervisor')
            ->where('id_supervisor', $userId)
            ->pluck('id_user_agent')
            ->toArray();

        // Total recuperado del mes por agentes supervisados
        $totalRecuperado = Payment::whereIn('id_agent', $agentIds)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('amount');

        // Total desembolsado del mes
        $totalDesembolsado = Credit::whereIn('id_agent', $agentIds)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('amount_neto');
            
        // Total de morosos
        $creditosMorosos = Credit::whereIn('id_agent', $agentIds)
            ->where('status', 'inprogress')
            ->whereRaw('DATEDIFF(NOW(), updated_at) > 7')
            ->get();
            
        $totalMorosos = count($creditosMorosos);

        // Porcentaje de recuperación
        $porcentajeRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2)
            : 0;

        // Clientes activos
        $clientesActivos = Credit::whereIn('id_agent', $agentIds)
            ->where('status', 'inprogress')
            ->count();

        // Últimos pagos
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
            
        // Total de morosos
        $creditosMorosos = Credit::where('id_agent', $userId)
            ->where('status', 'inprogress')
            ->whereRaw('DATEDIFF(NOW(), updated_at) > 7')
            ->get();
            
        $totalMorosos = count($creditosMorosos);

        // Porcentaje de recuperación
        $porcentajeRecuperacion = $totalDesembolsado > 0 
            ? round(($totalRecuperado / $totalDesembolsado) * 100, 2)
            : 0;

        // Clientes activos
        $clientesActivos = Credit::where('id_agent', $userId)
            ->where('status', 'inprogress')
            ->count();

        // Últimos pagos
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
            'totalMorosos' => $totalMorosos,
            'porcentajeRecuperacion' => $porcentajeRecuperacion,
            'clientesActivos' => $clientesActivos,
            'ultimosPagos' => $ultimosPagos,
            'morososRecientes' => $morososRecientes
        ];
    }
}
