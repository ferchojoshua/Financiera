<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WalletController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar listado de transacciones de billetera
     */
    public function index(Request $request)
    {
        // Ya no verificamos permisos específicos, solo autenticación (en el constructor)
        // if (!Auth::user()->can('view-wallet')) {
        //    return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección.');
        // }

        // Consulta principal de billeteras
        $query = Wallet::with('user')->select('id', 'user_id', 'balance', 'description', 'created_at');
        
        // Aplicar filtros si existen
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }
        
        $wallets = $query->orderBy('created_at', 'desc')->paginate(15);
        $users = User::where('status', 'active')->orderBy('name')->get();
        
        // Estadísticas
        $stats = [
            'total_balance' => Wallet::sum('balance'),
            'total_wallets' => Wallet::count(),
        ];
        
        return view('wallets.index', compact('wallets', 'users', 'stats'));
    }

    /**
     * Mostrar formulario para crear una nueva transacción
     */
    public function create()
    {
        // Verificar permiso
        if (!Auth::user()->can('create-wallet-transaction')) {
            return redirect()->route('wallets.index')->with('error', 'No tienes permisos para crear transacciones de billetera.');
        }
        
        $users = User::where('status', 'active')->orderBy('name')->get();
        return view('wallets.create', compact('users'));
    }

    /**
     * Almacenar una nueva transacción
     */
    public function store(Request $request)
    {
        // Verificar permiso
        if (!Auth::user()->can('create-wallet-transaction')) {
            return redirect()->route('wallets.index')->with('error', 'No tienes permisos para crear transacciones de billetera.');
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:deposit,withdrawal,transfer,payment,commission,other',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
            'transaction_date' => 'required|date',
            'status' => 'required|in:completed,pending,cancelled',
        ]);

        DB::beginTransaction();
        
        try {
            $wallet = new Wallet();
            $wallet->user_id = $request->user_id;
            $wallet->amount = $request->amount;
            $wallet->type = $request->type;
            $wallet->description = $request->description;
            $wallet->reference = $request->reference;
            $wallet->transaction_date = $request->transaction_date;
            $wallet->status = $request->status;
            $wallet->created_by = Auth::id();
            $wallet->save();
            
            DB::commit();
            return redirect()->route('wallets.index')->with('success', 'Transacción creada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la transacción: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mostrar detalle de una transacción
     */
    public function show($id)
    {
        // Verificar permiso
        if (!Auth::user()->can('view-wallet')) {
            return redirect()->route('wallets.index')->with('error', 'No tienes permisos para ver detalles de transacciones.');
        }
        
        $wallet = Wallet::with(['user', 'creator'])->findOrFail($id);
        return view('wallets.show', compact('wallet'));
    }

    /**
     * Mostrar formulario para editar una transacción
     */
    public function edit($id)
    {
        // Verificar permiso
        if (!Auth::user()->can('edit-wallet-transaction')) {
            return redirect()->route('wallets.index')->with('error', 'No tienes permisos para editar transacciones de billetera.');
        }
        
        $wallet = Wallet::findOrFail($id);
        $users = User::where('status', 'active')->orderBy('name')->get();
        
        return view('wallets.edit', compact('wallet', 'users'));
    }

    /**
     * Actualizar una transacción existente
     */
    public function update(Request $request, $id)
    {
        // Verificar permiso
        if (!Auth::user()->can('edit-wallet-transaction')) {
            return redirect()->route('wallets.index')->with('error', 'No tienes permisos para editar transacciones de billetera.');
        }
        
        $wallet = Wallet::findOrFail($id);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:deposit,withdrawal,transfer,payment,commission,other',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
            'transaction_date' => 'required|date',
            'status' => 'required|in:completed,pending,cancelled',
        ]);

        DB::beginTransaction();
        
        try {
            $wallet->user_id = $request->user_id;
            $wallet->amount = $request->amount;
            $wallet->type = $request->type;
            $wallet->description = $request->description;
            $wallet->reference = $request->reference;
            $wallet->transaction_date = $request->transaction_date;
            $wallet->status = $request->status;
            $wallet->updated_by = Auth::id();
            $wallet->save();
            
            DB::commit();
            return redirect()->route('wallets.index')->with('success', 'Transacción actualizada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la transacción: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Eliminar una transacción
     */
    public function destroy($id)
    {
        // Verificar permiso
        if (!Auth::user()->can('delete-wallet-transaction')) {
            return redirect()->route('wallets.index')->with('error', 'No tienes permisos para eliminar transacciones de billetera.');
        }
        
        $wallet = Wallet::findOrFail($id);
        
        try {
            $wallet->delete();
            return redirect()->route('wallets.index')->with('success', 'Transacción eliminada correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la transacción: ' . $e->getMessage());
        }
    }

    /**
     * Ver el balance de un usuario específico
     */
    public function userBalance($userId)
    {
        // Verificar permiso
        if (!Auth::user()->can('view-wallet')) {
            return redirect()->route('wallets.index')->with('error', 'No tienes permisos para ver el balance de usuarios.');
        }
        
        $user = User::findOrFail($userId);
        
        // Calcular balance
        $deposits = Wallet::where('user_id', $userId)
                         ->where('type', 'deposit')
                         ->where('status', 'completed')
                         ->sum('amount');
                         
        $withdrawals = Wallet::where('user_id', $userId)
                            ->where('type', 'withdrawal')
                            ->where('status', 'completed')
                            ->sum('amount');
                            
        $balance = $deposits - $withdrawals;
        
        // Obtener últimas transacciones
        $transactions = Wallet::where('user_id', $userId)
                             ->orderBy('transaction_date', 'desc')
                             ->limit(10)
                             ->get();
        
        return view('wallets.user_balance', compact('user', 'balance', 'deposits', 'withdrawals', 'transactions'));
    }

    /**
     * Generar informe de transacciones
     */
    public function report(Request $request)
    {
        // Verificar permiso
        if (!Auth::user()->can('view-wallet-reports')) {
            return redirect()->route('wallet.index')->with('error', 'No tienes permisos para generar informes de billetera.');
        }
        
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'type' => 'nullable|in:all,deposit,withdrawal,transfer,payment,commission,other',
            'user_id' => 'nullable|exists:users,id',
        ]);
        
        $query = Wallet::with('user')
                      ->whereBetween('transaction_date', [
                          $request->date_from . ' 00:00:00',
                          $request->date_to . ' 23:59:59'
                      ]);
        
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }
        
        $transactions = $query->orderBy('transaction_date')->get();
        
        // Estadísticas para el informe
        $stats = [
            'total_amount' => $transactions->sum('amount'),
            'count' => $transactions->count(),
            'by_type' => $transactions->groupBy('type')
                                    ->map(function ($group) {
                                        return [
                                            'count' => $group->count(),
                                            'amount' => $group->sum('amount')
                                        ];
                                    }),
        ];
        
        return view('wallet.report', compact('transactions', 'stats', 'request'));
    }
}
