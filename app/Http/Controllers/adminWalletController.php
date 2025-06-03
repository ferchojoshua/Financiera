<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class adminWalletController extends Controller
{
    /**
     * Constructor para aplicar middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,superadmin');
    }

    /**
     * Muestra el formulario para crear una nueva billetera
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Obtener usuarios que no tienen billetera asignada
        $users = User::whereNotIn('id', function($query) {
            $query->select('user_id')->from('wallets');
        })->get();

        // Obtener todas las billeteras existentes para referencia
        $wallets = Wallet::with('user')->get();

        return view('admin.wallet.create', compact('users', 'wallets'));
    }

    /**
     * Almacena una nueva billetera en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'initial_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255'
        ]);

        // Verificar si el usuario ya tiene una billetera
        $existingWallet = Wallet::where('user_id', $request->user_id)->first();
        if ($existingWallet) {
            return redirect()->back()->with('error', 'Este usuario ya tiene una billetera asignada.');
        }

        DB::beginTransaction();
        try {
            // Crear la billetera
            $wallet = new Wallet();
            $wallet->user_id = $request->user_id;
            $wallet->balance = $request->initial_balance;
            $wallet->description = $request->description;
            $wallet->created_by = Auth::id();
            $wallet->save();

            // Si hay saldo inicial, registrar la transacción
            if ($request->initial_balance > 0) {
                $transaction = new \App\Models\Transaction();
                $transaction->wallet_id = $wallet->id;
                $transaction->amount = $request->initial_balance;
                $transaction->type = 'deposit';
                $transaction->description = 'Saldo inicial';
                $transaction->created_by = Auth::id();
                $transaction->save();
            }

            DB::commit();
            return redirect()->route('admin.wallet.index')->with('success', 'Billetera creada con éxito.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al crear la billetera: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar listado de billeteras
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wallets = Wallet::with('user')->get();
        return view('admin.wallet.index', compact('wallets'));
    }

    /**
     * Mostrar detalle de una billetera específica
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $wallet = Wallet::with('user', 'transactions')->findOrFail($id);
        return view('admin.wallet.show', compact('wallet'));
    }

    /**
     * Mostrar formulario para editar una billetera
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $wallet = Wallet::with('user')->findOrFail($id);
        return view('admin.wallet.edit', compact('wallet'));
    }

    /**
     * Actualizar una billetera en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'nullable|string|max:255'
        ]);

        $wallet = Wallet::findOrFail($id);
        $wallet->description = $request->description;
        $wallet->save();

        return redirect()->route('admin.wallet.index')->with('success', 'Billetera actualizada con éxito.');
    }

    /**
     * Eliminar una billetera
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $wallet = Wallet::findOrFail($id);
        
        // Verificar si tiene transacciones
        if ($wallet->transactions()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar esta billetera porque tiene transacciones asociadas.');
        }
        
        $wallet->delete();
        return redirect()->route('admin.wallet.index')->with('success', 'Billetera eliminada con éxito.');
    }

    /**
     * Procesar un depósito en la billetera
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deposit(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255'
        ]);

        $wallet = Wallet::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Realizar el depósito
            $wallet->deposit($request->amount);
            
            // Registrar la transacción
            $transaction = new \App\Models\Transaction();
            $transaction->wallet_id = $wallet->id;
            $transaction->amount = $request->amount;
            $transaction->type = 'deposit';
            $transaction->description = $request->description ?: 'Depósito de fondos';
            $transaction->created_by = Auth::id();
            $transaction->save();
            
            DB::commit();
            return redirect()->route('admin.wallet.show', $wallet->id)->with('success', 'Depósito realizado con éxito.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al procesar el depósito: ' . $e->getMessage());
        }
    }

    /**
     * Procesar un retiro de la billetera
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function withdraw(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $wallet->balance,
            'description' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            // Realizar el retiro
            $success = $wallet->withdraw($request->amount);
            
            if (!$success) {
                return redirect()->back()->with('error', 'Fondos insuficientes para realizar el retiro.');
            }
            
            // Registrar la transacción
            $transaction = new \App\Models\Transaction();
            $transaction->wallet_id = $wallet->id;
            $transaction->amount = $request->amount;
            $transaction->type = 'withdrawal';
            $transaction->description = $request->description ?: 'Retiro de fondos';
            $transaction->created_by = Auth::id();
            $transaction->save();
            
            DB::commit();
            return redirect()->route('admin.wallet.show', $wallet->id)->with('success', 'Retiro realizado con éxito.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al procesar el retiro: ' . $e->getMessage());
        }
    }
} 