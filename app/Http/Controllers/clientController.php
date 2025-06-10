<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientRecord;
use App\Models\Credit;
use App\Models\LoanApplication;
use App\User;
use App\Models\CreditType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * Constructor del controlador
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar listado de clientes
     */
    public function index(Request $request)
    {
        // Verificar permisos de acceso
        if (!auth()->user()->hasModuleAccess('clientes')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a este módulo');
        }
        
        // Parámetros de filtrado
        $search = $request->input('search');
        $status = $request->input('status', 'active');
        
        // Consulta base
        $query = Client::query();
        
        // Aplicar filtros
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nit', 'like', "%{$search}%")
                  ->orWhere('dui', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%");
            });
        }
        
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($status === 'blacklisted') {
            $query->where('blacklisted', true);
        }
        
        // Si es un colector, mostrar solo sus clientes asignados
        if (auth()->user()->isColector()) {
            $query->where('assigned_agent_id', auth()->id());
        }
        
        // Ordenar y paginar resultados
        $clients = $query->orderBy('name')
                         ->orderBy('last_name')
                         ->paginate(10)
                         ->appends($request->query());
        
        return view('clients.index', compact('clients', 'search', 'status'));
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

    /**
     * Mostrar formulario para crear un nuevo cliente
     */
    public function create()
    {
        if (!auth()->user()->hasModuleAccess('clientes', 'create')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear clientes.');
        }

        try {
            // Obtener tipos de cliente
            $clientTypes = DB::table('client_types')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            
            // Obtener rutas
            $routes = DB::table('routes')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            
            // Obtener agentes (colectores)
            $agents = DB::table('users')
                ->select('id', 'name')
                ->where(function($query) {
                    $query->where('role', 'colector')
                          ->orWhere('level', 'colector');
                })
                ->orderBy('name')
                ->get();
            
            // Obtener tipos de crédito
            $creditTypes = CreditType::where('is_active', true)
                ->select('id', 'name', 'min_amount', 'max_amount', 'interest_rate')
                ->groupBy('name', 'id', 'min_amount', 'max_amount', 'interest_rate')
                ->orderBy('name')
                ->get();
            
            // Configuración de préstamos desde system_preferences
            $systemPreferences = DB::table('system_preferences')->first();
            
            $loanConfig = [
                'min_amount' => $systemPreferences->min_loan_amount ?? 100,
                'max_amount' => $systemPreferences->max_loan_amount ?? 5000,
                'interest_rates' => [10, 15, 20], // Valores por defecto
                'payment_frequencies' => [
                    'daily' => 'Diario',
                    'weekly' => 'Semanal',
                    'biweekly' => 'Quincenal',
                    'monthly' => 'Mensual'
                ]
            ];
            
            return view('clients.create', compact('clientTypes', 'routes', 'agents', 'creditTypes', 'loanConfig'));
            
        } catch (\Exception $e) {
            \Log::error("Error en create: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Almacenar un nuevo cliente
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasModuleAccess('clientes', 'create')) {
            return back()->with('error', 'No tienes permisos para crear clientes.');
        }
        
        try {
            // Log para diagnóstico
            Log::info('Request completo:', [
                'all' => $request->all(),
                'input' => $request->input(),
                'post' => $_POST,
                'files' => $request->allFiles()
            ]);
            
            // Validar datos
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:clients',
                'phone' => 'required|string|max:20',
                'nit' => 'required|string|max:20',
                'dui' => 'nullable|string|max:20',
                'address' => 'required|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
                'birthdate' => 'nullable|date',
                'gender' => 'nullable|in:M,F',
                'civil_status' => 'nullable|in:soltero,casado,divorciado,viudo,union_libre',
                'house_type' => 'nullable|in:propia,alquilada,familiar',
                'spouse_name' => 'nullable|required_if:civil_status,casado|string|max:255',
                'spouse_job' => 'nullable|string|max:255',
                'spouse_phone' => 'nullable|string|max:20',
                'business_name' => 'nullable|string|max:255',
                'business_type' => 'nullable|string|max:255',
                'business_time' => 'nullable|integer',
                'business_sector' => 'nullable|string|max:255',
                'economic_activity' => 'nullable|string|max:255',
                'annual_revenue' => 'nullable|numeric',
                'employee_count' => 'nullable|integer',
                'founding_date' => 'nullable|date',
                'sales_good' => 'nullable|numeric',
                'sales_bad' => 'nullable|numeric',
                'weekly_average' => 'nullable|numeric',
                'net_profit' => 'nullable|numeric',
                'route_id' => 'nullable|exists:routes,id',
                'notes' => 'nullable|string',
                // Campos de la solicitud de crédito
                'credit_type_id' => 'required_with:loan_amount|exists:credit_types,id',
                'loan_amount' => 'required_with:credit_type_id|numeric|min:0',
                'term_months' => 'required_with:loan_amount|integer|min:1',
                'payment_frequency' => 'required_with:loan_amount|in:daily,weekly,biweekly,monthly'
            ]);
            
            // Log datos validados
            Log::info('Datos validados:', $validated);
            
            // Validar límites de monto si se proporcionó un monto
            if ($request->filled('loan_amount') && $request->filled('credit_type_id')) {
                $creditType = CreditType::findOrFail($request->credit_type_id);
                
                if ($request->loan_amount < $creditType->min_amount || 
                    $request->loan_amount > $creditType->max_amount) {
                    return back()
                        ->withInput()
                        ->withErrors(['loan_amount' => 'El monto debe estar entre $' . 
                            number_format($creditType->min_amount, 2) . ' y $' . 
                            number_format($creditType->max_amount, 2)]);
                }
                
                if ($request->term_months < $creditType->min_term_months || 
                    $request->term_months > $creditType->max_term_months) {
                    return back()
                        ->withInput()
                        ->withErrors(['term_months' => 'El plazo debe estar entre ' . 
                            $creditType->min_term_months . ' y ' . 
                            $creditType->max_term_months . ' meses']);
                }
            }
            
            // Iniciar transacción
            DB::beginTransaction();
            
            try {
                // Crear el cliente
                $client = new Client();
                $client->fill($validated);
                
                // Asignaciones y estado
                $client->is_active = true;
                $client->created_by = Auth::id();
                $client->credit_score = 70; // Score inicial por defecto
                $client->status = 'active';
                
                // Log para depuración
                Log::info('Datos del cliente antes de guardar:', $client->toArray());
                
                // Guardar el cliente
                $client->save();
                
                // Log para depuración
                Log::info('Cliente guardado con ID: ' . $client->id);
                
                // Crear registro inicial en el expediente
                $client->addRecord(
                    ClientRecord::TYPE_NOTE,
                    'Cliente creado en el sistema.',
                    ClientRecord::STATUS_ACTIVE
                );
                
                // Si se proporcionó un monto, crear la solicitud de crédito
                if ($request->filled('loan_amount')) {
                    $application = new LoanApplication();
                    $application->client_id = $client->id;
                    $application->credit_type_id = $request->input('credit_type_id');
                    $application->amount_requested = $request->input('loan_amount');
                    $application->term_months = $request->input('term_months');
                    $application->payment_frequency = $request->input('payment_frequency');
                    $application->status = 'pending';
                    $application->notes = $request->input('notes');
                    $application->created_by = Auth::id();
                    
                    // Log para depuración
                    Log::info('Datos de la solicitud antes de guardar:', $application->toArray());
                    
                    $application->save();
                    
                    // Log para depuración
                    Log::info('Solicitud guardada con ID: ' . $application->id);
                    
                    // Registrar la solicitud en el expediente
                    $client->addRecord(
                        ClientRecord::TYPE_CREDIT,
                        "Solicitud de crédito creada por $" . number_format($request->loan_amount, 2),
                        ClientRecord::STATUS_PENDING
                    );
                }
                
                // Confirmar transacción
                DB::commit();
                
                return redirect()->route('clients.show', ['client' => $client->id])
                    ->with('success', 'Cliente registrado correctamente. ' . 
                        ($request->filled('loan_amount') ? 'Se ha creado una solicitud de crédito.' : ''));
                    
            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollBack();
                Log::error("Error al crear cliente: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Error al crear el cliente: ' . $e->getMessage());
            }
                
        } catch (\Exception $e) {
            // Log detallado del error
            Log::error("Error al crear cliente: " . $e->getMessage());
            Log::error("Archivo: " . $e->getFile() . " Línea: " . $e->getLine());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el cliente: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar información de un cliente
     */
    public function show($id)
    {
        // Verificar permisos
        if (!auth()->user()->hasModuleAccess('clientes')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para ver clientes');
        }
        
        // Buscar el cliente
        $client = Client::findOrFail($id);
        
        // Cargar relaciones
        $client->load('records', 'assignedAgent', 'createdBy');
        
        // Obtener créditos del cliente
        $credits = Credit::where('client_id', $client->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        // Calcular puntaje de crédito
        $creditScore = $client->credit_score ?? $client->calculateCreditScore();
        
        // Verificar elegibilidad para nuevo crédito
        $isEligible = $client->isEligibleForNewCredit();
        
        return view('clients.show', compact(
            'client', 
            'credits', 
            'creditScore',
            'isEligible'
        ));
    }

    /**
     * Mostrar formulario para editar un cliente
     */
    public function edit($id)
    {
        // Verificar permisos
        if (!auth()->user()->hasModuleAccess('clientes')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para editar clientes');
        }
        
        try {
            // Buscar el cliente
            $client = Client::findOrFail($id);
            
            // Obtener tipos de cliente
            $clientTypes = DB::table('client_types')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            
            // Obtener rutas
            $routes = DB::table('routes')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            
            // Obtener agentes (colectores)
            $agents = DB::table('users')
                ->select('id', 'name')
                ->where(function($query) {
                    $query->where('role', 'colector')
                          ->orWhere('level', 'colector');
                })
                ->orderBy('name')
                ->get();
            
            // Obtener tipos de crédito
            $creditTypes = CreditType::where('is_active', true)
                ->select('id', 'name', 'min_amount', 'max_amount', 'interest_rate')
                ->groupBy('name', 'id', 'min_amount', 'max_amount', 'interest_rate')
                ->orderBy('name')
                ->get();
            
            // Configuración de préstamos desde system_preferences
            $systemPreferences = DB::table('system_preferences')->first();
            
            $loanConfig = [
                'min_amount' => $systemPreferences->min_loan_amount ?? 100,
                'max_amount' => $systemPreferences->max_loan_amount ?? 5000,
                'interest_rates' => [10, 15, 20], // Valores por defecto
                'payment_frequencies' => [
                    'daily' => 'Diario',
                    'weekly' => 'Semanal',
                    'biweekly' => 'Quincenal',
                    'monthly' => 'Mensual'
                ]
            ];
            
            return view('clients.edit', compact(
                'client',
                'clientTypes',
                'routes',
                'agents',
                'creditTypes',
                'loanConfig'
            ));
            
        } catch (\Exception $e) {
            \Log::error("Error en edit: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un cliente existente
     */
    public function update(Request $request, $id)
    {
        // Verificar permisos
        if (!auth()->user()->hasModuleAccess('clientes')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para editar clientes');
        }
        
        // Buscar el cliente
        $client = Client::findOrFail($id);
        
        try {
            // Validar datos
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:clients,email,' . $id,
                'phone' => 'required|string|max:20',
                'nit' => 'required|string|max:20',
                'dui' => 'nullable|string|max:20',
                'address' => 'required|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
                'birthdate' => 'nullable|date',
                'gender' => 'nullable|in:M,F',
                'civil_status' => 'nullable|in:soltero,casado,divorciado,viudo,union_libre',
                'house_type' => 'nullable|in:propia,alquilada,familiar',
                'spouse_name' => 'nullable|required_if:civil_status,casado|string|max:255',
                'spouse_job' => 'nullable|string|max:255',
                'spouse_phone' => 'nullable|string|max:20',
                'business_name' => 'nullable|string|max:255',
                'business_type' => 'nullable|string|max:255',
                'business_time' => 'nullable|integer',
                'business_sector' => 'nullable|string|max:255',
                'economic_activity' => 'nullable|string|max:255',
                'annual_revenue' => 'nullable|numeric',
                'employee_count' => 'nullable|integer',
                'founding_date' => 'nullable|date',
                'sales_good' => 'nullable|numeric',
                'sales_bad' => 'nullable|numeric',
                'weekly_average' => 'nullable|numeric',
                'net_profit' => 'nullable|numeric',
                'route_id' => 'nullable|exists:routes,id',
                'notes' => 'nullable|string'
            ]);
            
            // Iniciar transacción
            DB::beginTransaction();
            
            // Guardar datos anteriores para registro de cambios
            $oldData = $client->getAttributes();
            
            // Actualizar el cliente
            $client->fill($validated);
            $client->updated_by = auth()->id();
            $client->save();
            
            // Registrar cambios significativos
            $this->logSignificantChanges($client, $oldData);
            
            // Confirmar transacción
            DB::commit();
            
            return redirect()->route('clients.show', ['client' => $client->id])
                ->with('success', 'Cliente actualizado exitosamente');
                
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            Log::error("Error al actualizar cliente: " . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el cliente: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar un cliente
     */
    public function destroy(Client $client)
    {
        // Verificar permisos
        if (!auth()->user()->hasModuleAccess('clientes') || !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para eliminar clientes');
        }
        
        try {
            // Iniciar transacción
            DB::beginTransaction();
            
            // Eliminar registros relacionados
            $client->records()->delete();
            
            // Eliminar el cliente
            $client->delete();
            
            // Confirmar transacción
            DB::commit();
            
            return redirect()->route('clients.index')
                ->with('success', 'Cliente eliminado correctamente');
                
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            Log::error("Error al eliminar cliente: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Agregar nota al expediente del cliente
     */
    public function addRecord(Request $request, Client $client)
    {
        // Verificar permisos
        if (!auth()->user()->hasModuleAccess('clientes')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para modificar expedientes');
        }
        
        // Validar datos
        $request->validate([
            'record_type' => 'required|string',
            'description' => 'required|string',
            'record_status' => 'nullable|string'
        ]);
        
        try {
            // Crear nuevo registro en el expediente
            $client->addRecord(
                $request->record_type,
                $request->description,
                $request->record_status ?? ClientRecord::STATUS_ACTIVE
            );
            
            // Si es una nota de lista negra, actualizar el cliente
            if ($request->record_type === ClientRecord::TYPE_BLACKLIST) {
                $client->blacklisted = true;
                $client->blacklist_reason = $request->description;
                $client->save();
            }
            
            return redirect()->route('clients.show', $client)
                ->with('success', 'Nota agregada al expediente correctamente');
                
        } catch (\Exception $e) {
            Log::error("Error al agregar nota al expediente: " . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al agregar nota al expediente: ' . $e->getMessage());
        }
    }

    /**
     * Reactivar un cliente que estaba en lista negra
     */
    public function reactivate($id)
    {
        // Verificar permisos
        if (!auth()->user()->hasModuleAccess('clientes') || !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para reactivar clientes');
        }
        
        // Buscar el cliente
        $client = Client::findOrFail($id);
        
        try {
            // Quitar de lista negra
            $client->blacklisted = false;
            $client->blacklist_reason = null;
            $client->is_active = true;
            $client->save();
            
            // Agregar nota al expediente
            $client->addRecord(
                ClientRecord::TYPE_NOTE,
                'Cliente reactivado y removido de lista negra por ' . auth()->user()->name,
                ClientRecord::STATUS_IMPORTANT
            );
            
            return redirect()->route('clients.show', ['client' => $client->id])
                ->with('success', 'Cliente reactivado correctamente');
                
        } catch (\Exception $e) {
            Log::error("Error al reactivar cliente: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Error al reactivar el cliente: ' . $e->getMessage()]);
        }
    }

    /**
     * Validar datos del cliente
     */
    private function validateClientData(Request $request, $clientId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email,' . $clientId,
            'phone' => 'required|string|max:20',
            'nit' => 'required|string|max:20',
            'dui' => 'nullable|string|max:20',
            'address' => 'required|string',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
            'civil_status' => 'nullable|in:soltero,casado,divorciado,viudo,union_libre',
            
            // Datos del cónyuge
            'spouse_name' => 'nullable|required_if:civil_status,casado|string|max:255',
            'spouse_job' => 'nullable|string|max:255',
            'spouse_phone' => 'nullable|string|max:20',
            
            // Datos de ubicación adicionales
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            
            // Datos del negocio
            'business_type' => 'nullable|string|max:100',
            'business_time' => 'nullable|integer',
            'sales_good' => 'nullable|numeric',
            'sales_bad' => 'nullable|numeric',
            'weekly_average' => 'nullable|numeric',
            'net_profit' => 'nullable|numeric',
            
            // Datos de crédito
            'loan_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'payment_frequency' => 'nullable|in:daily,weekly,biweekly,monthly',
            'first_payment_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string'
        ];
        
        return $request->validate($rules);
    }

    /**
     * Registrar cambios significativos en los datos del cliente
     */
    private function logSignificantChanges(Client $client, array $oldData)
    {
        $changes = [];
        
        // Campos a monitorear
        $fieldsToMonitor = [
            'name' => 'nombre',
            'last_name' => 'apellido',
            'nit' => 'NIT',
            'dui' => 'DUI',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'address' => 'dirección',
            'business_type' => 'tipo de negocio',
            'business_time' => 'tiempo del negocio',
            'sales_good' => 'ventas buenas',
            'sales_bad' => 'ventas malas',
            'weekly_average' => 'promedio semanal',
            'net_profit' => 'ganancia neta'
        ];
        
        foreach ($fieldsToMonitor as $field => $label) {
            if (isset($oldData[$field]) && $client->$field !== $oldData[$field]) {
                $changes[] = "Cambio en {$label}: de '{$oldData[$field]}' a '{$client->$field}'";
            }
        }
        
        // Si hay cambios, registrarlos en el expediente
        if (!empty($changes)) {
            $description = "Se realizaron los siguientes cambios:\n" . implode("\n", $changes);
            
            $client->addRecord(
                ClientRecord::TYPE_NOTE,
                $description,
                ClientRecord::STATUS_IMPORTANT
            );
        }
    }

    /**
     * Reporte de rendimiento de clientes.
     */
    public function performance(Request $request)
    {
        // Lógica del reporte de rendimiento (a desarrollar)
        // Por ahora, solo devolvemos la vista con datos de ejemplo.
        $clients = Client::withCount('credits')->orderBy('credits_count', 'desc')->take(10)->get();

        return view('clients.performance', compact('clients'));
    }

    /**
     * Reporte general de clientes.
     */
    public function report(Request $request)
    {
        // Lógica del reporte de clientes (a desarrollar)
        $totalClients = Client::count();
        $activeClients = Client::where('is_active', true)->count();
        
        return view('clients.report', compact('totalClients', 'activeClients'));
    }
} 