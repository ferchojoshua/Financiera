<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContabilidadController extends Controller
{
    /**
     * Muestra la vista principal de contabilidad
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtener datos contables básicos
        if (Schema::hasTable('cash_register')) {
            // Si existe la tabla cash_register, usamos esa para los ingresos
            $ingresos = DB::table('cash_register')
                ->where('type', 'ingreso')
                ->select(DB::raw('SUM(amount) as total'))
                ->first();
        } else {
            // Si no existe, usamos todos los registros de summary como ingresos
            $ingresos = DB::table('summary')
                ->select(DB::raw('SUM(amount) as total'))
                ->first();
        }
            
        $gastos = DB::table('bills')
            ->select(DB::raw('SUM(amount) as total'))
            ->first();
            
        $creditos = DB::table('credit')
            ->select(DB::raw('COUNT(id) as total, SUM(amount_neto) as monto_total'))
            ->first();
            
        $pagos = DB::table('summary')
            ->select(DB::raw('COUNT(id) as total, SUM(amount) as monto_total'))
            ->first();
            
        return view('contabilidad.index', compact('ingresos', 'gastos', 'creditos', 'pagos'));
    }
    
    /**
     * Muestra el reporte de ingresos
     *
     * @return \Illuminate\Http\Response
     */
    public function ingresos()
    {
        if (Schema::hasTable('cash_register')) {
            // Si existe la tabla cash_register, usamos esa para los ingresos
            $ingresos = DB::table('cash_register')
                ->where('type', 'ingreso')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Si no existe, usamos todos los registros de summary como ingresos
            $ingresos = DB::table('summary')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }
            
        return view('contabilidad.ingresos', compact('ingresos'));
    }
    
    /**
     * Muestra el reporte de gastos
     *
     * @return \Illuminate\Http\Response
     */
    public function gastos()
    {
        if (Schema::hasTable('cash_register')) {
            // Si existe la tabla cash_register, verificamos si hay egresos
            $hasExpenses = DB::table('cash_register')
                ->where('type', 'egreso')
                ->exists();
                
            if ($hasExpenses) {
                $gastos = DB::table('cash_register')
                    ->where('type', 'egreso')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                    
                return view('contabilidad.gastos', compact('gastos'));
            }
        }
        
        // Si no hay registros de egresos en cash_register o la tabla no existe, 
        // usamos la tabla bills para gastos
        $gastos = DB::table('bills')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('contabilidad.gastos', compact('gastos'));
    }
    
    /**
     * Muestra el balance general
     *
     * @return \Illuminate\Http\Response
     */
    public function balance()
    {
        if (Schema::hasTable('cash_register')) {
            // Si existe la tabla cash_register, usamos esa para los ingresos y egresos
            $ingresos = DB::table('cash_register')
                ->where('type', 'ingreso')
                ->select(DB::raw('SUM(amount) as total'))
                ->first();
                
            $gastos = DB::table('cash_register')
                ->where('type', 'egreso')
                ->select(DB::raw('SUM(amount) as total'))
                ->first();
        } else {
            // Si no existe, usamos todos los registros de summary como ingresos
            $ingresos = DB::table('summary')
                ->select(DB::raw('SUM(amount) as total'))
                ->first();
                
            $gastos = DB::table('bills')
                ->select(DB::raw('SUM(amount) as total'))
                ->first();
        }
            
        $balance = ($ingresos->total ?? 0) - ($gastos->total ?? 0);
        
        return view('contabilidad.balance', compact('ingresos', 'gastos', 'balance'));
    }
}
