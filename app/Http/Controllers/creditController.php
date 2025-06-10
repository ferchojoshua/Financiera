<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CreditCreated;
use App\Notifications\CreditApproved;
use App\Notifications\CreditRejected;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Credit::with(['client', 'agent', 'approver'])->latest();

        // Aplicar filtro por estado
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Aplicar filtro de búsqueda
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('amount', 'like', "%{$searchTerm}%")
                  ->orWhereHas('client', function($clientQuery) use ($searchTerm) {
                      $clientQuery->where('name', 'like', "%{$searchTerm}%")
                                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                                  ->orWhere('nit', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $credits = $query->paginate(10)->withQueryString();
            
        return view('credit.index', compact('credits'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->hasModuleAccess('creditos', 'create')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear créditos.');
        }

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
        if (!auth()->user()->hasModuleAccess('creditos', 'create')) {
            return back()->with('error', 'No tienes permisos para crear créditos.');
        }
        
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'payment_frequency' => 'required|in:daily,weekly,biweekly,monthly',
            'first_payment_date' => 'required|date|after:today',
            'id_wallet' => 'required|exists:wallet,id',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $credit = new Credit();
            $credit->client_id = $request->client_id;
            $credit->id_user = Auth::id();
            $credit->amount = $request->amount;
            $credit->interest_rate = $request->interest_rate;
            $credit->payment_frequency = $request->payment_frequency;
            $credit->first_payment_date = $request->first_payment_date;
            $credit->id_wallet = $request->id_wallet;
            $credit->status = 'pending';
            $credit->notes = $request->notes;
            $credit->created_by = Auth::id();
            $credit->save();

            // Notificar al cliente y al administrador
            $client = $credit->client;
            $admins = User::where('role', 'admin')->get();
            
            Notification::send($client, new CreditCreated($credit));
            Notification::send($admins, new CreditCreated($credit));

            DB::commit();

            return redirect()->route('credits.show', $credit->id)
                ->with('success', 'Crédito creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al crear el crédito: ' . $e->getMessage());
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
        $credit = Credit::with(['user', 'wallet', 'agent', 'approver'])
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
        
        $clients = Client::orderBy('name')->get();
            
        $wallets = Wallet::with('user')
            ->whereIn('status', ['activa', 'active', 'completed'])
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
            'client_id' => 'required|exists:clients,id',
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
            $credit->client_id = $request->client_id;
            $credit->id_wallet = $request->id_wallet;
            $credit->amount = $request->amount;
            $credit->amount_neto = $amountNeto;
            $credit->utility = $request->utility;
            $credit->period = $request->period;
            $credit->payment_frequency = $request->payment_frequency;
            $credit->payment_number = $request->payment_number;
            $credit->payment_amount = $paymentAmount;
            $credit->status = $request->status;
            
            // Si se modificó el crédito, volver a poner en pendiente de aprobación
            if ($credit->isDirty() && $credit->status === 'aprobado') {
                $credit->status = 'pendiente';
                $credit->approved_by = null;
                $credit->approved_at = null;
                $credit->approved_notes = null;
                
                // Notificar a los supervisores sobre la modificación
                $this->notifySupervisorsAboutCredit($credit, true);
            }
            
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
    
    /**
     * Mostrar créditos pendientes de aprobación
     */
    public function pendingApproval()
    {
        // Verificar que el usuario tenga rol de supervisor o admin
        if (!Auth::user()->hasRole('supervisor') && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home')
                ->with('error', 'No tiene permisos para acceder a esta sección.');
        }
        
        $credits = Credit::with(['user', 'agent'])
            ->where('status', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('credit.pending_approval', compact('credits'));
    }
    
    /**
     * Mostrar formulario para aprobar/rechazar un crédito
     */
    public function showApprovalForm($id)
    {
        // Verificar que el usuario tenga rol de supervisor o admin
        if (!Auth::user()->hasRole('supervisor') && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home')
                ->with('error', 'No tiene permisos para acceder a esta sección.');
        }
        
        $credit = Credit::with(['user', 'agent', 'wallet'])
            ->findOrFail($id);
            
        if ($credit->status !== 'pendiente') {
            return redirect()->route('credit.index')
                ->with('error', 'Este crédito ya ha sido procesado.');
        }
        
        return view('credit.approve', compact('credit'));
    }
    
    /**
     * Procesar la aprobación o rechazo de un crédito
     */
    public function processApproval(Request $request, $id)
    {
        // Verificar que el usuario tenga rol de supervisor o admin
        if (!Auth::user()->hasRole('supervisor') && !Auth::user()->hasRole('admin')) {
            return redirect()->route('home')
                ->with('error', 'No tiene permisos para acceder a esta sección.');
        }
        
        $request->validate([
            'decision' => 'required|in:aprobado,rechazado',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            DB::beginTransaction();
            
            $credit = Credit::with('user', 'agent')->findOrFail($id);
            
            if ($credit->status !== 'pendiente') {
                return redirect()->route('credit.pending_approval')
                    ->with('error', 'Este crédito ya ha sido procesado.');
            }
            
            $credit->status = $request->decision;
            $credit->approved_by = Auth::id();
            $credit->approved_at = now();
            $credit->approved_notes = $request->notes;
            
            // Si fue rechazado, cambiar el estado del crédito
            if ($request->decision === 'rechazado') {
                $credit->status = 'cancelled';
            }
            
            $credit->save();
            
            // Enviar notificaciones según la decisión
            if ($request->decision === 'aprobado') {
                $this->notifyAboutApproval($credit);
            } else {
                $this->notifyAboutRejection($credit);
            }
            
            DB::commit();
            
            return redirect()->route('credit.pending_approval')
                ->with('success', 'Crédito ' . ($request->decision === 'aprobado' ? 'aprobado' : 'rechazado') . ' correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Notificar a los supervisores sobre un nuevo crédito
     */
    private function notifySupervisorsAboutCredit($credit, $isUpdate = false)
    {
        // Obtener todos los usuarios con rol de supervisor o admin
        $supervisors = User::whereHas('roles', function($query) {
            $query->whereIn('slug', ['supervisor', 'admin']);
        })->get();
        
        // Si existieran notificaciones implementadas, se usarían así
        // Notification::send($supervisors, new CreditCreated($credit, $isUpdate));
        
        // Por ahora simplemente registramos la notificación en el log
        \Log::info('Nuevo crédito pendiente de aprobación: #' . $credit->id . ' para el cliente ' . $credit->user->name);
    }
    
    /**
     * Notificar sobre la aprobación de un crédito
     */
    private function notifyAboutApproval($credit)
    {
        // Notificar al agente que creó el crédito
        // Si existieran notificaciones implementadas, se usarían así
        // Notification::send($credit->agent, new CreditApproved($credit));
        
        // Por ahora simplemente registramos la notificación en el log
        \Log::info('Crédito #' . $credit->id . ' aprobado para el cliente ' . $credit->user->name);
    }
    
    /**
     * Notificar sobre el rechazo de un crédito
     */
    private function notifyAboutRejection($credit)
    {
        // Notificar al agente que creó el crédito
        // Si existieran notificaciones implementadas, se usarían así
        // Notification::send($credit->agent, new CreditRejected($credit));
        
        // Por ahora simplemente registramos la notificación en el log
        \Log::info('Crédito #' . $credit->id . ' rechazado para el cliente ' . $credit->user->name);
    }

    /**
     * Mostrar el formulario para crear un nuevo crédito para un cliente específico.
     *
     * @param  int  $client_id
     * @return \Illuminate\Http\Response
     */
    public function createForClient($client_id)
    {
        $client = \App\Models\Client::findOrFail($client_id);
        
        // Verificar si el cliente es elegible para un nuevo crédito
        if (!$client->isEligibleForNewCredit()) {
            return redirect()->route('clients.show', $client_id)
                ->with('error', 'El cliente no es elegible para un nuevo crédito en este momento.');
        }
        
        // Obtener las rutas disponibles
        $routes = \App\Models\Route::where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('credits.create', compact('client', 'routes'));
    }

    /**
     * Muestra la página para gestionar los tipos de cliente.
     */
    public function types()
    {
        if (!auth()->user()->hasModuleAccess('clientes', 'view')) { // O un permiso más específico si existe
            return redirect()->route('home')->with('error', 'No tienes permisos para gestionar tipos de cliente.');
        }

        $types = DB::table('client_types')->orderBy('name')->get();

        return view('client.types', compact('types'));
    }

    /**
     * Almacena un nuevo tipo de cliente.
     */
    public function storeType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:client_types',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        DB::table('client_types')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => $request->has('is_active'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tipo de cliente creado correctamente.');
    }

    /**
     * Actualiza un tipo de cliente existente.
     */
    public function updateType(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:client_types,name,' . $id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        DB::table('client_types')->where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tipo de cliente actualizado correctamente.');
    }

    /**
     * Elimina un tipo de cliente.
     */
    public function destroyType($id)
    {
        // Opcional: Verificar si el tipo está en uso antes de borrar
        $is_in_use = Client::where('client_type_id', $id)->exists();

        if ($is_in_use) {
            return response()->json(['error' => 'No se puede eliminar el tipo porque está en uso.'], 422);
        }

        DB::table('client_types')->where('id', $id)->delete();

        return response()->json(['success' => 'Tipo de cliente eliminado correctamente.']);
    }
}
