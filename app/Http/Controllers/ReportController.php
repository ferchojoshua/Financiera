<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Credit;
use App\Models\Payment;
use App\Models\Route;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Muestra la página principal de reportes
     */
    public function index()
    {
        return view('reports.index');
    }
    
    /**
     * Reporte de préstamos activos
     */
    public function active(Request $request)
    {
        try {
            $query = Credit::where('status', 'active');
                    
            // Filtros adicionales
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }
            
            if ($request->has('route_id') && $request->route_id) {
                $query->where('route_id', $request->route_id);
            }
            
            if (Schema::hasTable('routes') && $request->has('collector_id') && $request->collector_id) {
                $query->whereHas('route', function($q) use ($request) {
                    $q->where('collected_by', $request->collector_id);
                });
            }
            
            if ($request->has('date_filter') && $request->date_filter) {
                switch ($request->date_filter) {
                    case 'today':
                        $query->whereDate('created_at', date('Y-m-d'));
                        break;
                    case 'this_week':
                        $query->whereBetween('created_at', [
                            now()->startOfWeek(), 
                            now()->endOfWeek()
                        ]);
                        break;
                    case 'this_month':
                        $query->whereMonth('created_at', date('m'))
                              ->whereYear('created_at', date('Y'));
                        break;
                    case 'last_month':
                        $query->whereMonth('created_at', now()->subMonth()->month)
                              ->whereYear('created_at', now()->subMonth()->year);
                        break;
                    case 'this_year':
                        $query->whereYear('created_at', date('Y'));
                        break;
                }
            }
            
            $credits = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Obtener rutas y cobradores para los filtros
            try {
                $routes = Route::orderBy('name')->get();
            } catch (\Exception $e) {
                $routes = collect([]);
            }
            
            try {
                $collectors = User::where('level', 'agent')
                            ->orderBy('name')
                            ->get();
            } catch (\Exception $e) {
                $collectors = collect([]);
            }
            
            // Estadísticas para el dashboard
            $stats = [
                'total_credits' => $query->count(),
                'total_amount' => $query->sum('amount'),
                'total_interest' => $query->sum('utility_rate') * $query->sum('amount') / 100, // Calculado usando tasa de utilidad
                'avg_amount' => $query->count() > 0 ? $query->avg('amount') : 0,
            ];
            
            return view('reports.active', compact('credits', 'routes', 'collectors', 'stats'));
        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en reporte de préstamos activos: ' . $e->getMessage());
            
            // Devolver vista con mensaje de error
            return redirect()->route('reports.index')
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

/**
     * Reporte de desembolsos
     */
    public function disbursements(Request $request)
    {
        try {
            $startDate = $request->input('start_date', date('Y-m-01'));
            $endDate = $request->input('end_date', date('Y-m-d'));
            
            $query = Credit::where('status', 'active');
            
            // Verificar si la columna existe para evitar errores SQL
            if (Schema::hasColumn('credit', 'disbursement_date')) {
                $query->whereNotNull('disbursement_date')
                      ->whereDate('disbursement_date', '>=', $startDate)
                      ->whereDate('disbursement_date', '<=', $endDate);
            }
                
            // Filtros adicionales
            if ($request->has('user_id') && $request->user_id) {
                $query->where('client_id', $request->user_id);
            }
            
            if ($request->has('branch_id') && $request->branch_id) {
                $query->where('branch_id', $request->branch_id);
            }
            
            $credits = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Obtener las sucursales para el filtro
            try {
                $branches = \App\Models\Branch::orderBy('name')->get();
            } catch (\Exception $e) {
                $branches = collect([]);
            }
            
            // Estadísticas para la vista
            $stats = [
                'total_amount' => $credits->sum('amount'),
                'count' => $credits->count(),
                'avg_amount' => $credits->count() > 0 ? $credits->sum('amount') / $credits->count() : 0,
                'new_clients' => $credits->where('is_renewal', false)->count()
            ];
            
            // Pasar las variables a la vista
            return view('reports.disbursements', compact('credits', 'startDate', 'endDate', 'branches', 'stats'));
        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en reporte de desembolsos: ' . $e->getMessage());
            
            // Devolver vista con mensaje de error
            return redirect()->route('reports.index')
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

/**
     * Reporte de préstamos vencidos
     */
    public function overdue(Request $request)
    {
        try {
            $query = Credit::where('status', 'active');
            
            // Verificar si la columna existe para evitar errores SQL
            if (Schema::hasColumn('credit', 'disbursement_date')) {
                $query->whereNotNull('disbursement_date')
                      ->whereRaw('DATE_ADD(disbursement_date, INTERVAL 30 DAY) < CURDATE()');
                      
                // Filtros adicionales
                if ($request->has('days_overdue') && $request->days_overdue) {
                    $daysRange = explode('-', $request->days_overdue);
                    if (count($daysRange) == 2) {
                        $minDays = intval($daysRange[0]);
                        $maxDays = intval($daysRange[1]);
                        $query->whereRaw("DATEDIFF(CURDATE(), DATE_ADD(disbursement_date, INTERVAL 30 DAY)) BETWEEN $minDays AND $maxDays");
                    } else if (count($daysRange) == 1 && str_ends_with($request->days_overdue, '+')) {
                        $minDays = intval($daysRange[0]);
                        $query->whereRaw("DATEDIFF(CURDATE(), DATE_ADD(disbursement_date, INTERVAL 30 DAY)) >= $minDays");
                    }
                }
            }
                    
            // Filtros adicionales
            if ($request->has('user_id') && $request->user_id) {
                $query->where('client_id', $request->user_id);
            }
            
            if (Schema::hasColumn('credit', 'disbursement_date')) {
                $credits = $query->orderByRaw('DATEDIFF(CURDATE(), DATE_ADD(disbursement_date, INTERVAL 30 DAY)) DESC')->paginate(20);
                
                // Añadir la propiedad days_overdue a cada crédito
                $credits->each(function($credit) {
                    if (isset($credit->disbursement_date)) {
                        $credit->days_overdue = \DB::raw("DATEDIFF(CURDATE(), DATE_ADD(disbursement_date, INTERVAL 30 DAY))");
                        // Calcular la fecha de vencimiento (30 días después del desembolso)
                        $credit->due_date = \Carbon\Carbon::parse($credit->disbursement_date)->addDays(30);
                    } else {
                        $credit->days_overdue = 0;
                        $credit->due_date = \Carbon\Carbon::now();
                    }
                });
                
                // Estadísticas para la vista
                $stats = [
                    'total_overdue_amount' => $query->sum('amount'),
                    'total_credits' => $query->count(),
                    'avg_days_overdue' => $query->count() > 0 ? $query->avg(\DB::raw("DATEDIFF(CURDATE(), DATE_ADD(disbursement_date, INTERVAL 30 DAY))")) : 0,
                    'max_days_overdue' => $query->count() > 0 ? $query->max(\DB::raw("DATEDIFF(CURDATE(), DATE_ADD(disbursement_date, INTERVAL 30 DAY))")) : 0
                ];
            } else {
                $credits = $query->orderBy('created_at', 'desc')->paginate(20);
                
                // Estadísticas básicas si no existe la columna
                $stats = [
                    'total_overdue_amount' => $query->sum('amount'),
                    'total_credits' => $query->count(),
                    'avg_days_overdue' => 0,
                    'max_days_overdue' => 0
                ];
            }
            
            // Obtener rutas y cobradores para los filtros
            try {
                $routes = Route::orderBy('name')->get();
            } catch (\Exception $e) {
                $routes = collect([]);
            }
            
            try {
                $collectors = User::where('level', 'agent')
                            ->orderBy('name')
                            ->get();
            } catch (\Exception $e) {
                $collectors = collect([]);
            }
            
            return view('reports.overdue', compact('credits', 'routes', 'collectors', 'stats'));
        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en reporte de préstamos vencidos: ' . $e->getMessage());
            
            // Devolver vista con mensaje de error
            return redirect()->route('reports.index')
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

/**
     * Reporte de cierre de mes
     */
    public function monthlyClose(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));
            
            // Resumen de préstamos del mes
            $loansSummary = Credit::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->select(
                        DB::raw('COUNT(*) as total_loans'),
                        DB::raw('SUM(amount) as total_amount'),
                        DB::raw('AVG(utility_rate) as avg_interest_rate'),
                        DB::raw('COUNT(CASE WHEN status = "active" THEN 1 END) as active_loans'),
                        DB::raw('COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_loans')
                    )
                    ->first();
            
            // Resumen de pagos del mes
            $paymentsSummary = Payment::whereMonth('payment_date', $month)
                    ->whereYear('payment_date', $year)
                    ->select(
                        DB::raw('COUNT(*) as total_payments'),
                        DB::raw('SUM(amount) as total_amount'),
                        DB::raw('SUM(CASE WHEN type = "principal" THEN amount ELSE 0 END) as principal_amount'),
                        DB::raw('SUM(CASE WHEN type = "interest" THEN amount ELSE 0 END) as interest_amount'),
                        DB::raw('SUM(CASE WHEN type = "late_fee" THEN amount ELSE 0 END) as late_fee_amount')
                    )
                    ->first();
            
            return view('reports.monthly_close', compact('loansSummary', 'paymentsSummary', 'month', 'year'));
        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en reporte de cierre mensual: ' . $e->getMessage());
            
            // Devolver vista con mensaje de error
            return redirect()->route('reports.index')
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

/**
     * Reporte de recuperación y desembolsos
     */
    public function recoveryAndDisbursements(Request $request)
    {
        try {
            $startDate = $request->input('start_date', date('Y-m-01'));
            $endDate = $request->input('end_date', date('Y-m-d'));
            
            // Desembolsos en el período
            $disbursements = Credit::whereNotNull('disbursement_date')
                    ->whereDate('disbursement_date', '>=', $startDate)
                    ->whereDate('disbursement_date', '<=', $endDate)
                    ->select(
                        DB::raw('DATE(disbursement_date) as date'),
                        DB::raw('SUM(amount_neto) as total_amount'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy(DB::raw('DATE(disbursement_date)'))
                    ->orderBy('date')
                    ->get();
            
            // Recuperaciones (pagos) en el período
            $recoveries = Payment::whereDate('payment_date', '>=', $startDate)
                    ->whereDate('payment_date', '<=', $endDate)
                    ->select(
                        DB::raw('DATE(payment_date) as date'),
                        DB::raw('SUM(amount) as total_amount'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy(DB::raw('DATE(payment_date)'))
                    ->orderBy('date')
                    ->get();
            
            // Formato para gráficos
            $dates = [];
            $disbursementData = [];
            $recoveryData = [];
            
            $currentDate = strtotime($startDate);
            $endTimestamp = strtotime($endDate);
            
            while ($currentDate <= $endTimestamp) {
                $dateStr = date('Y-m-d', $currentDate);
                $dates[] = date('d/m/Y', $currentDate);
                
                // Buscar si hay desembolsos en esta fecha
                $disbAmount = 0;
                foreach ($disbursements as $disb) {
                    if ($disb->date == $dateStr) {
                        $disbAmount = $disb->total_amount;
                        break;
                    }
                }
                $disbursementData[] = $disbAmount;
                
                // Buscar si hay recuperaciones en esta fecha
                $recAmount = 0;
                foreach ($recoveries as $rec) {
                    if ($rec->date == $dateStr) {
                        $recAmount = $rec->total_amount;
                        break;
                    }
                }
                $recoveryData[] = $recAmount;
                
                $currentDate = strtotime('+1 day', $currentDate);
            }
            
            return view('reports.recovery_and_disbursements', compact(
                'disbursements', 'recoveries', 'startDate', 'endDate',
                'dates', 'disbursementData', 'recoveryData'
            ));
        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en reporte de recuperación y desembolsos: ' . $e->getMessage());
            
            // Devolver vista con mensaje de error
            return redirect()->route('reports.index')
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Reporte de préstamos cancelados
     */
    public function cancelled(Request $request)
    {
        try {
            $query = Credit::where('status', 'cancelled');
                    
            // Filtros adicionales
            if ($request->has('user_id') && $request->user_id) {
                $query->where('client_id', $request->user_id);
            }
            
            if ($request->has('route_id') && $request->route_id) {
                $query->where('route_id', $request->route_id);
            }
            
            if (Schema::hasTable('routes') && $request->has('collector_id') && $request->collector_id) {
                $query->whereHas('route', function($q) use ($request) {
                    $q->where('collected_by', $request->collector_id);
                });
            }
            
            if ($request->has('date_filter') && $request->date_filter) {
                switch ($request->date_filter) {
                    case 'today':
                        $query->whereDate('updated_at', date('Y-m-d'));
                        break;
                    case 'this_week':
                        $query->whereBetween('updated_at', [
                            now()->startOfWeek(), 
                            now()->endOfWeek()
                        ]);
                        break;
                    case 'this_month':
                        $query->whereMonth('updated_at', date('m'))
                              ->whereYear('updated_at', date('Y'));
                        break;
                    case 'last_month':
                        $query->whereMonth('updated_at', now()->subMonth()->month)
                              ->whereYear('updated_at', now()->subMonth()->year);
                        break;
                    case 'this_year':
                        $query->whereYear('updated_at', date('Y'));
                        break;
                }
            }
            
            $credits = $query->orderBy('updated_at', 'desc')->paginate(20);
            
            // Obtener rutas y cobradores para los filtros
            try {
                $routes = Route::orderBy('name')->get();
            } catch (\Exception $e) {
                $routes = collect([]);
            }
            
            try {
                $collectors = User::where('level', 'agent')
                            ->orderBy('name')
                            ->get();
            } catch (\Exception $e) {
                $collectors = collect([]);
            }
            
            // Estadísticas para el dashboard
            $stats = [
                'total_credits' => $query->count(),
                'total_amount' => $query->sum('amount'),
                'total_interest' => $query->sum('utility_rate') * $query->sum('amount') / 100, // Calculado usando tasa de utilidad
                'avg_days_to_cancel' => $query->count() > 0 ? $query->avg(DB::raw('DATEDIFF(updated_at, created_at)')) : 0,
            ];
            
            return view('reports.cancelled', compact('credits', 'routes', 'collectors', 'stats'));
        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en reporte de préstamos cancelados: ' . $e->getMessage());
            
            // Devolver vista con mensaje de error
            return redirect()->route('reports.index')
                ->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }
}
