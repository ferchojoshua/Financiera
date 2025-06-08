<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountingEntry;
use App\Models\Credit;
use App\Models\Payment;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar la vista principal de contabilidad
     */
    public function index(Request $request)
    {
        $query = AccountingEntry::query();

        // Aplicar filtros si existen
        if ($request->filled('start_date')) {
            $query->where('entry_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('entry_date', '<=', $request->end_date);
        }
        if ($request->filled('type')) {
            $query->where('entry_type', $request->type);
        }

        $entries = $query->orderBy('entry_date', 'desc')->paginate(15);

        // Calcular totales
        $totals = [
            'ingresos' => AccountingEntry::where('entry_type', 'ingreso')->sum('amount'),
            'gastos' => AccountingEntry::where('entry_type', 'gasto')->sum('amount'),
            'ajustes' => AccountingEntry::where('entry_type', 'ajuste')->sum('amount')
        ];

        return view('accounting.index', compact('entries', 'totals'));
    }

    /**
     * Mostrar el formulario para crear una nueva entrada contable
     */
    public function create()
    {
        return view('accounting.create');
    }

    /**
     * Almacenar una nueva entrada contable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'entry_type' => 'required|in:ingreso,gasto,ajuste',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'reference' => 'nullable|string',
            'accounting_account' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $entry = new AccountingEntry($validated);
        $entry->created_by = Auth::id();
        $entry->save();

        return redirect()->route('accounting.index')
            ->with('success', 'Entrada contable registrada correctamente');
    }

    /**
     * Mostrar una entrada contable específica
     */
    public function show($id)
    {
        $entry = AccountingEntry::findOrFail($id);
        return view('accounting.show', compact('entry'));
    }

    /**
     * Mostrar el formulario para editar una entrada contable
     */
    public function edit($id)
    {
        $entry = AccountingEntry::findOrFail($id);
        return view('accounting.edit', compact('entry'));
    }

    /**
     * Actualizar una entrada contable
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'entry_type' => 'required|in:ingreso,gasto,ajuste',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'reference' => 'nullable|string',
            'accounting_account' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $entry = AccountingEntry::findOrFail($id);
        $entry->update($validated);

        return redirect()->route('accounting.index')
            ->with('success', 'Entrada contable actualizada correctamente');
    }

    /**
     * Eliminar una entrada contable
     */
    public function destroy($id)
    {
        $entry = AccountingEntry::findOrFail($id);
        $entry->delete();

        return redirect()->route('accounting.index')
            ->with('success', 'Entrada contable eliminada correctamente');
    }

    /**
     * Mostrar el cierre mensual
     */
    public function monthClose(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Obtener totales del mes
        $totals = [
            'ingresos' => AccountingEntry::where('entry_type', 'ingreso')
                ->whereBetween('entry_date', [$startDate, $endDate])
                ->sum('amount'),
            'gastos' => AccountingEntry::where('entry_type', 'gasto')
                ->whereBetween('entry_date', [$startDate, $endDate])
                ->sum('amount'),
            'ajustes' => AccountingEntry::where('entry_type', 'ajuste')
                ->whereBetween('entry_date', [$startDate, $endDate])
                ->sum('amount')
        ];

        // Obtener entradas agrupadas por categoría
        $entriesByCategory = AccountingEntry::whereBetween('entry_date', [$startDate, $endDate])
            ->select('category', 'entry_type', DB::raw('SUM(amount) as total'))
            ->groupBy('category', 'entry_type')
            ->get();

        return view('accounting.month-close', compact('totals', 'entriesByCategory', 'month', 'year'));
    }

    /**
     * Mostrar los desembolsos
     */
    public function disbursements(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());

        $disbursements = Credit::with(['user', 'route'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $total = $disbursements->sum('amount_neto');

        return view('accounting.disbursements', compact('disbursements', 'total'));
    }

    /**
     * Exportar desembolsos
     */
    public function exportDisbursements(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());

        $disbursements = Credit::with(['user', 'route'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'active')
            ->get();

        // Aquí iría la lógica de exportación según el formato requerido
        // Por ahora retornamos a la vista con un mensaje
        return redirect()->route('accounting.disbursements')
            ->with('info', 'Función de exportación en desarrollo');
    }
} 