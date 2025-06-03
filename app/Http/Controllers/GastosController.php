<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GastosController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Filtros
        $fechaDesde = $request->input('fecha_desde') ? Carbon::createFromFormat('d/m/Y', $request->input('fecha_desde'))->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $fechaHasta = $request->input('fecha_hasta') ? Carbon::createFromFormat('d/m/Y', $request->input('fecha_hasta'))->endOfDay() : Carbon::now()->endOfDay();
        $categoria = $request->input('categoria');

        // Consulta base
        $query = DB::table('gastos')
            ->whereDate('fecha', '>=', $fechaDesde)
            ->whereDate('fecha', '<=', $fechaHasta);

        // Aplicar filtro de categoría si existe
        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        // Obtener gastos paginados
        $gastos = $query->orderBy('fecha', 'desc')
            ->paginate(10);

        // Estadísticas
        $gastosHoy = DB::table('gastos')
            ->whereDate('fecha', Carbon::today())
            ->sum('monto');

        $gastosMes = DB::table('gastos')
            ->whereYear('fecha', Carbon::now()->year)
            ->whereMonth('fecha', Carbon::now()->month)
            ->sum('monto');

        $totalGastos = DB::table('gastos')
            ->sum('monto');

        $presupuesto = 5000; // Ejemplo de presupuesto fijo

        // Estadísticas por categoría
        $gastosOficina = DB::table('gastos')
            ->where('categoria', 'oficina')
            ->sum('monto');

        $gastosServicios = DB::table('gastos')
            ->where('categoria', 'servicios')
            ->sum('monto');

        $gastosNomina = DB::table('gastos')
            ->where('categoria', 'nomina')
            ->sum('monto');

        $gastosImpuestos = DB::table('gastos')
            ->where('categoria', 'impuestos')
            ->sum('monto');

        $gastosOtros = DB::table('gastos')
            ->where('categoria', 'otros')
            ->sum('monto');

        // Estadísticas por mes
        $year = Carbon::now()->year;
        $gastosEnero = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 1)
            ->sum('monto');

        $gastosFebrero = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 2)
            ->sum('monto');

        $gastosMarzo = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 3)
            ->sum('monto');

        $gastosAbril = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 4)
            ->sum('monto');

        $gastosMayo = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 5)
            ->sum('monto');

        $gastosJunio = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 6)
            ->sum('monto');

        $gastosJulio = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 7)
            ->sum('monto');

        $gastosAgosto = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 8)
            ->sum('monto');

        $gastosSeptiembre = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 9)
            ->sum('monto');

        $gastosOctubre = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 10)
            ->sum('monto');

        $gastosNoviembre = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 11)
            ->sum('monto');

        $gastosDiciembre = DB::table('gastos')
            ->whereYear('fecha', $year)
            ->whereMonth('fecha', 12)
            ->sum('monto');

        return view('gastos.index', compact(
            'gastos', 
            'gastosHoy', 
            'gastosMes', 
            'totalGastos', 
            'presupuesto',
            'gastosOficina',
            'gastosServicios',
            'gastosNomina',
            'gastosImpuestos',
            'gastosOtros',
            'gastosEnero',
            'gastosFebrero',
            'gastosMarzo',
            'gastosAbril',
            'gastosMayo',
            'gastosJunio',
            'gastosJulio',
            'gastosAgosto',
            'gastosSeptiembre',
            'gastosOctubre',
            'gastosNoviembre',
            'gastosDiciembre'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('gastos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date_format:d/m/Y',
            'descripcion' => 'required|string|max:255',
            'categoria' => 'required|string|in:oficina,servicios,nomina,impuestos,otros',
            'monto' => 'required|numeric|min:0.01',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Procesar fecha
        $fecha = Carbon::createFromFormat('d/m/Y', $request->fecha)->format('Y-m-d');

        // Manejar archivo de comprobante si existe
        $comprobantePath = null;
        if ($request->hasFile('comprobante')) {
            $comprobante = $request->file('comprobante');
            $comprobantePath = $comprobante->store('comprobantes', 'public');
        }

        // Crear registro de gasto
        $gastoId = DB::table('gastos')->insertGetId([
            'fecha' => $fecha,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'monto' => $request->monto,
            'comprobante' => $comprobantePath,
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('gastos.index')->with('success', 'Gasto registrado correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $gasto = DB::table('gastos')->where('id', $id)->first();
        
        if (!$gasto) {
            return redirect()->route('gastos.index')->with('error', 'Gasto no encontrado');
        }
        
        return view('gastos.show', compact('gasto'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gasto = DB::table('gastos')->where('id', $id)->first();
        
        if (!$gasto) {
            return redirect()->route('gastos.index')->with('error', 'Gasto no encontrado');
        }
        
        return view('gastos.edit', compact('gasto'));
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
        $gasto = DB::table('gastos')->where('id', $id)->first();
        
        if (!$gasto) {
            return redirect()->route('gastos.index')->with('error', 'Gasto no encontrado');
        }
        
        $request->validate([
            'fecha' => 'required|date_format:d/m/Y',
            'descripcion' => 'required|string|max:255',
            'categoria' => 'required|string|in:oficina,servicios,nomina,impuestos,otros',
            'monto' => 'required|numeric|min:0.01',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Procesar fecha
        $fecha = Carbon::createFromFormat('d/m/Y', $request->fecha)->format('Y-m-d');

        // Manejar archivo de comprobante si existe
        $comprobantePath = $gasto->comprobante;
        if ($request->hasFile('comprobante')) {
            // Eliminar comprobante anterior si existe
            if ($comprobantePath) {
                Storage::disk('public')->delete($comprobantePath);
            }
            
            // Guardar nuevo comprobante
            $comprobante = $request->file('comprobante');
            $comprobantePath = $comprobante->store('comprobantes', 'public');
        }

        // Actualizar registro de gasto
        DB::table('gastos')
            ->where('id', $id)
            ->update([
                'fecha' => $fecha,
                'descripcion' => $request->descripcion,
                'categoria' => $request->categoria,
                'monto' => $request->monto,
                'comprobante' => $comprobantePath,
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('gastos.index')->with('success', 'Gasto actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gasto = DB::table('gastos')->where('id', $id)->first();
        
        if (!$gasto) {
            return redirect()->route('gastos.index')->with('error', 'Gasto no encontrado');
        }
        
        // Eliminar comprobante si existe
        if ($gasto->comprobante) {
            Storage::disk('public')->delete($gasto->comprobante);
        }
        
        // Eliminar gasto
        DB::table('gastos')->where('id', $id)->delete();
        
        return redirect()->route('gastos.index')->with('success', 'Gasto eliminado correctamente');
    }
} 