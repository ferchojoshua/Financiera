<?php

namespace App\Http\Controllers;

use App\User;
use App\db_wallet;
use App\db_credit;
use App\db_summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class adminStatisticsController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalUsers = User::count();
        $totalWallets = db_wallet::count();
        $totalCredits = db_credit::count();
        
        // Monto total de créditos
        $totalAmount = db_credit::sum('amount_neto');
        
        // Créditos por estado
        $creditsByStatus = db_credit::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
            
        // Usuarios por nivel
        $usersByLevel = User::select('level', DB::raw('count(*) as total'))
            ->groupBy('level')
            ->get();
            
        // Carteras más activas
        $activeWallets = db_wallet::withCount('credits')
            ->orderBy('credits_count', 'desc')
            ->take(5)
            ->get();
            
        // Resumen de pagos
        $paymentSummary = db_summary::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        return view('admin.statistics.index', compact(
            'totalUsers',
            'totalWallets',
            'totalCredits',
            'totalAmount',
            'creditsByStatus',
            'usersByLevel',
            'activeWallets',
            'paymentSummary'
        ));
    }
} 