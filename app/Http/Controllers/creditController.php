<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $credits = Credit::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('credit.index', compact('credits'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Obtener todos los clientes para seleccionar
        $clients = User::where('role', 'user')
            ->orderBy('name')
            ->get();
            
        // Obtener las billeteras disponibles
        $wallets = Wallet::where('status', 'activa')
            ->orWhere('status', 'completed')
            ->orWhere('status', 'active')
            ->get();
            
        // Obtener todas las rutas activas
        $routes = \App\Models\Route::where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('credit.create', compact('clients', 'wallets', 'routes'));
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
            'id_user' => 'required|exists:users,id',
            'id_wallet' => 'required|exists:db_wallet,id',
            'amount' => 'required|numeric|min:1',
            'utility' => 'required|numeric|min:0',
            'period' => 'required|numeric|min:1',
            'payment_frequency' => 'required|in:diario,semanal,quincenal,mensual',
            'payment_number' => 'required|numeric|min:1',
            'route_id' => 'nullable|exists:routes,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Calcular monto neto y por cuota
            $amountNeto = $request->amount + ($request->amount * $request->utility / 100);
            $paymentAmount = $amountNeto / $request->payment_number;
            
            // Crear el crédito
            $credit = new Credit();
            $credit->id_user = $request->id_user;
            $credit->id_agent = Auth::id();
            $credit->id_wallet = $request->id_wallet;
            $credit->amount = $request->amount;
            $credit->amount_neto = $amountNeto;
            $credit->utility = $request->utility;
            $credit->period = $request->period;
            $credit->payment_frequency = $request->payment_frequency;
            $credit->payment_number = $request->payment_number;
            $credit->payment_amount = $paymentAmount;
            $credit->status = 'inprogress';
            
            // Guardar la ruta si se proporcionó
            if ($request->has('route_id') && !empty($request->route_id)) {
                $credit->route_id = $request->route_id;
            }
            
            $credit->save();
            
            DB::commit();
            
            return redirect()->route('credit.index')
                ->with('success', 'Solicitud de crédito creada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear la solicitud de crédito: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $credit = Credit::with('user', 'wallet', 'agent')
            ->findOrFail($id);
            
        // Obtener los pagos realizados para este crédito
        $payments = DB::table('summary')
            ->where('id_credit', $id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('credit.show', compact('credit', 'payments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $credit = Credit::findOrFail($id);
        
        // Solo permitir editar créditos en progreso
        if ($credit->status != 'inprogress') {
            return redirect()->route('credit.index')
                ->with('error', 'Solo se pueden editar créditos en progreso.');
        }
        
        $clients = User::where('role', 'user')
            ->orderBy('name')
            ->get();
            
        $wallets = Wallet::where('status', 'activa')
            ->get();
            
        return view('credit.edit', compact('credit', 'clients', 'wallets'));
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
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_wallet' => 'required|exists:db_wallet,id',
            'amount' => 'required|numeric|min:1',
            'utility' => 'required|numeric|min:0',
            'period' => 'required|numeric|min:1',
            'payment_frequency' => 'required|in:diario,semanal,quincenal,mensual',
            'payment_number' => 'required|numeric|min:1',
            'status' => 'required|in:inprogress,completed,cancelled'
        ]);
        
        try {
            DB::beginTransaction();
            
            $credit = Credit::findOrFail($id);
            
            // Calcular monto neto y por cuota
            $amountNeto = $request->amount + ($request->amount * $request->utility / 100);
            $paymentAmount = $amountNeto / $request->payment_number;
            
            // Actualizar el crédito
            $credit->id_user = $request->id_user;
            $credit->id_wallet = $request->id_wallet;
            $credit->amount = $request->amount;
            $credit->amount_neto = $amountNeto;
            $credit->utility = $request->utility;
            $credit->period = $request->period;
            $credit->payment_frequency = $request->payment_frequency;
            $credit->payment_number = $request->payment_number;
            $credit->payment_amount = $paymentAmount;
            $credit->status = $request->status;
            $credit->save();
            
            DB::commit();
            
            return redirect()->route('credit.index')
                ->with('success', 'Solicitud de crédito actualizada correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la solicitud de crédito: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $credit = Credit::findOrFail($id);
            
            // Verificar si tiene pagos asociados
            $hasSummaries = DB::table('summary')
                ->where('id_credit', $id)
                ->exists();
                
            if ($hasSummaries) {
                return redirect()->route('credit.index')
                    ->with('error', 'No se puede eliminar el crédito porque tiene pagos asociados.');
            }
            
            $credit->delete();
            
            return redirect()->route('credit.index')
                ->with('success', 'Solicitud de crédito eliminada correctamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('credit.index')
                ->with('error', 'Error al eliminar la solicitud de crédito: ' . $e->getMessage());
        }
    }
}
