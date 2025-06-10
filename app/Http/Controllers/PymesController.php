<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Credit;
use App\Models\LoanApplication;
use App\Models\ClientDocument;
use App\Models\FinancialStatement;
use App\Models\CreditScoring;
use App\Models\Collateral;
use App\Models\PaymentSchedule;
use App\Models\CollectionAction;
use App\Models\PaymentAgreement;
use App\Models\AccountingEntry;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PymesController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:approve-loans')->only(['solicitudesApprove']);
        $this->middleware('permission:reject-loans')->only(['solicitudesReject']);
    }

    /**
     * Mostrar listado de clientes PYME
     */
    public function clientes()
    {
        // Obtener usuarios tipo PYME
        $clientes = User::where('level', 'user')
                      ->whereNotNull('business_name')
                      ->orderBy('business_name')
                      ->paginate(10);

        return view('pymes.clientes.index', compact('clientes'));
    }

    /**
     * Mostrar formulario para crear cliente PYME
     */
    public function clientesCreate()
    {
        return view('pymes.clientes.create');
    }

    /**
     * Almacenar un nuevo cliente PYME
     */
    public function clientesStore(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:20|unique:users,tax_id',
            'contact_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'sector' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = new User();
        $user->name = $request->contact_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->level = 'user';
        $user->business_name = $request->business_name;
        $user->tax_id = $request->tax_id;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->sector = $request->sector;
        $user->save();

        return redirect()->route('pymes.clientes')->with('success', 'Cliente PYME creado correctamente');
    }

    /**
     * Mostrar detalles de un cliente PYME
     */
    public function clientesShow($id)
    {
        $cliente = User::findOrFail($id);
        $creditos = Credit::where('id_user', $id)->get();
        
        return view('pymes.clientes.show', compact('cliente', 'creditos'));
    }

    /**
     * Mostrar solicitudes de crédito
     */
    public function solicitudes()
    {
        // Obtener solicitudes
        $solicitudes = LoanApplication::with(['client', 'analyst'])
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(10);

        return view('pymes.solicitudes.index', compact('solicitudes'));
    }

    /**
     * Mostrar formulario para crear solicitud de crédito
     */
    public function solicitudesCreate()
    {
        $clientes = User::where('level', 'user')
                    ->whereNotNull('business_name')
                    ->orderBy('business_name')
                    ->get();
                    
        return view('pymes.solicitudes.create', compact('clientes'));
    }

    /**
     * Almacenar una nueva solicitud de crédito
     */
    public function solicitudesStore(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
            'term' => 'required|integer|min:1|max:60',
            'purpose' => 'required|string',
            'purpose_detail' => 'required_if:purpose,otro|nullable|string',
            'description' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        // Crear la solicitud de crédito
        $solicitud = new LoanApplication();
        $solicitud->client_id = $request->client_id;
        $solicitud->amount = $request->amount;
        $solicitud->term_months = $request->term;
        $solicitud->purpose = $request->purpose;
        $solicitud->purpose_detail = $request->purpose_detail;
        $solicitud->description = $request->description;
        $solicitud->status = 'pending';
        $solicitud->analyst_id = auth()->id(); // Asignar al usuario actual como analista
        $solicitud->application_date = now(); // Establecer la fecha de solicitud
        $solicitud->save();

        // Procesar documentos si existen
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('documents/solicitudes/' . $solicitud->id, 'public');
                
                $doc = new ClientDocument();
                $doc->loan_application_id = $solicitud->id;
                $doc->file_path = $path;
                $doc->file_name = $document->getClientOriginalName();
                $doc->file_type = $document->getClientOriginalExtension();
                $doc->file_size = $document->getSize();
                $doc->uploaded_by = auth()->id();
                $doc->save();
            }
        }

        return redirect()->route('pymes.solicitudes')->with('success', 'Solicitud de crédito creada correctamente');
    }

    /**
     * Mostrar detalles de una solicitud de crédito
     */
    public function solicitudesShow($id)
    {
        $solicitud = LoanApplication::with(['client', 'analyst', 'documents', 'credit'])
                                  ->findOrFail($id);
                                  
        return view('pymes.solicitudes.show', compact('solicitud'));
    }

    /**
     * Aprobar una solicitud de crédito
     */
    public function solicitudesApprove(Request $request, $id)
    {
        \Log::info('Iniciando aprobación de solicitud', [
            'id' => $id,
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'approval_notes' => 'nullable|string|max:1000',
            ]);

            DB::transaction(function() use ($request, $id) {
                $solicitud = LoanApplication::findOrFail($id);
                
                \Log::info('Solicitud encontrada', [
                    'solicitud_id' => $solicitud->id,
                    'status' => $solicitud->status,
                    'client_id' => $solicitud->client_id
                ]);
                
                if ($solicitud->status !== 'pending' && $solicitud->status !== 'under_review') {
                    throw new \Exception('Solo se pueden aprobar solicitudes pendientes o en revisión.');
                }

                // Verificar si ya existe un crédito
                if ($solicitud->credit()->exists()) {
                    throw new \Exception('Esta solicitud ya tiene un crédito asociado.');
                }

                // Actualizar la solicitud
                $solicitud->status = 'approved';
                $solicitud->approved_by = auth()->id();
                $solicitud->approval_date = now();
                $solicitud->approval_notes = $request->approval_notes;
                $solicitud->save();

                \Log::info('Solicitud actualizada', [
                    'new_status' => $solicitud->status,
                    'approved_by' => $solicitud->approved_by
                ]);

                // Generar número de crédito
                $creditNumber = 'CR-' . str_pad($solicitud->id, 6, '0', STR_PAD_LEFT);

                // Crear el crédito
                $credit = new Credit();
                $credit->client_id = $solicitud->client_id;
                $credit->id_user = $solicitud->client_id;
                $credit->credit_number = $creditNumber;
                $credit->amount = $solicitud->amount_requested;
                $credit->amount_approved = $solicitud->amount_requested;
                $credit->payment_number = $solicitud->term_months;
                $credit->interest_rate = $solicitud->interest_rate;
                $credit->payment_frequency = 'monthly';
                $credit->first_payment_date = now()->addMonth();
                $credit->status = 'active';
                $credit->approved_by = auth()->id();
                $credit->approved_at = now();
                $credit->save();

                // Actualizar la solicitud con el ID del crédito
                $solicitud->credit_id = $credit->id;
                $solicitud->save();

                // Registrar la acción en el historial
                $solicitud->history()->create([
                    'action' => 'approve',
                    'user_id' => auth()->id(),
                    'notes' => $request->approval_notes,
                    'data' => json_encode([
                        'credit_id' => $credit->id,
                        'credit_number' => $credit->credit_number,
                        'amount' => $credit->amount_approved,
                        'payment_number' => $credit->payment_number
                    ])
                ]);

                // Intentar generar plan de pagos si el método existe
                if (method_exists($credit, 'generatePaymentSchedule')) {
                    $credit->generatePaymentSchedule();
                } elseif (method_exists($credit, 'generatePaymentPlan')) {
                    $credit->generatePaymentPlan();
                }
            });

            $response = [
                'success' => true,
                'message' => 'Solicitud aprobada correctamente y crédito creado.',
                'redirect' => route('pymes.solicitudes.show', $id)
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($response);
            }

            return redirect()
                ->route('pymes.solicitudes.show', $id)
                ->with('success', $response['message']);

        } catch (\Exception $e) {
            \Log::error('Error al aprobar solicitud: ' . $e->getMessage(), [
                'exception' => $e,
                'id' => $id,
                'user_id' => auth()->id()
            ]);
            
            $response = [
                'success' => false,
                'message' => 'Error al aprobar la solicitud: ' . $e->getMessage()
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($response, 422);
            }
            
            return back()
                ->withInput()
                ->with('error', $response['message']);
        }
    }

    /**
     * Rechazar una solicitud de crédito
     */
    public function solicitudesReject(Request $request, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);

            DB::transaction(function() use ($request, $id) {
                $solicitud = LoanApplication::findOrFail($id);
                
                if ($solicitud->status !== 'pending' && $solicitud->status !== 'under_review') {
                    throw new \Exception('Solo se pueden rechazar solicitudes pendientes o en revisión.');
                }

                // Actualizar la solicitud
                $solicitud->status = 'rejected';
                $solicitud->rejected_by = auth()->id();
                $solicitud->rejection_date = now();
                $solicitud->rejection_reason = $request->rejection_reason;
                $solicitud->save();

                // Registrar la acción en el historial
                $solicitud->history()->create([
                    'action' => 'reject',
                    'user_id' => auth()->id(),
                    'notes' => $request->rejection_reason,
                    'data' => json_encode([
                        'reason' => $request->rejection_reason
                    ])
                ]);
            });

            $response = [
                'success' => true,
                'message' => 'Solicitud rechazada correctamente.',
                'redirect' => route('pymes.solicitudes.show', $id)
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($response);
            }

            return redirect()
                ->route('pymes.solicitudes.show', $id)
                ->with('success', $response['message']);

        } catch (\Exception $e) {
            \Log::error('Error al rechazar solicitud: ' . $e->getMessage());

            $response = [
                'success' => false,
                'message' => 'Error al rechazar la solicitud: ' . $e->getMessage()
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($response, 422);
            }

            return back()
                ->withInput()
                ->with('error', $response['message']);
        }
    }

    /**
     * Mostrar análisis y scoring
     */
    public function analisis()
    {
        // Obtener análisis de crédito
        $scorings = CreditScoring::with(['user', 'loanApplication', 'analyst'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

        return view('pymes.analisis.index', compact('scorings'));
    }

    /**
     * Mostrar formulario para crear análisis de crédito
     */
    public function analisisCreate($solicitud_id)
    {
        $solicitud = LoanApplication::with(['client', 'documents', 'financialStatements'])
                                  ->findOrFail($solicitud_id);
        
        return view('pymes.analisis.create', compact('solicitud'));
    }

    /**
     * Mostrar detalles de un análisis de crédito
     */
    public function analisisShow($id)
    {
        $analisis = CreditScoring::with(['user', 'loanApplication', 'analyst'])
                               ->findOrFail($id);
                               
        return view('pymes.analisis.show', compact('analisis'));
    }

    /**
     * Mostrar garantías
     */
    public function garantias()
    {
        // Obtener garantías
        $garantias = Collateral::with(['user', 'credit'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        return view('pymes.garantias.index', compact('garantias'));
    }

    /**
     * Mostrar formulario para crear garantía
     */
    public function garantiasCreate()
    {
        $clientes = User::where('level', 'user')
                    ->whereNotNull('business_name')
                    ->orderBy('business_name')
                    ->get();
                    
        $creditos = Credit::where('status', 'inprogress')
                       ->get();
                       
        return view('pymes.garantias.create', compact('clientes', 'creditos'));
    }

    /**
     * Mostrar detalles de una garantía
     */
    public function garantiasShow($id)
    {
        $garantia = Collateral::with(['user', 'credit'])
                           ->findOrFail($id);
                           
        return view('pymes.garantias.show', compact('garantia'));
    }

    /**
     * Mostrar productos financieros
     */
    public function productos()
    {
        // Aquí se mostrarían las configuraciones de productos
        return view('pymes.productos.index');
    }

    /**
     * Mostrar formulario para crear producto financiero
     */
    public function productosCreate()
    {
        return view('pymes.productos.create');
    }

    /**
     * Almacenar un nuevo producto financiero
     */
    public function productosStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tasa_interes' => 'required|numeric|min:0|max:100',
            'plazo_maximo' => 'required|integer|min:1|max:120',
            'monto_minimo' => 'required|numeric|min:0',
            'monto_maximo' => 'required|numeric|min:0|gt:monto_minimo',
            'comision_apertura' => 'nullable|numeric|min:0|max:10',
            'requisitos' => 'nullable|string',
            'activo' => 'nullable|boolean',
        ]);

        // Aquí normalmente crearíamos el producto en la base de datos
        // En este caso, solo retornamos con un mensaje de éxito simulando la creación

        return redirect()->route('pymes.productos')->with('success', 'Producto financiero creado correctamente');
    }

    /**
     * Mostrar detalles de un producto financiero
     */
    public function productosShow($id)
    {
        // Aquí normalmente buscaríamos el producto por ID
        // Como no hay un modelo definido, simulamos con datos de ejemplo
        $producto = (object)[
            'id' => $id,
            'nombre' => 'Producto ' . $id,
            'tasa_interes' => 10.5,
            'plazo_maximo' => 36,
            'monto_minimo' => 5000,
            'monto_maximo' => 50000,
            'activo' => true
        ];
        
        return view('pymes.productos.show', compact('producto'));
    }

    /**
     * Mostrar módulo de contabilidad
     */
    public function contabilidad(Request $request)
    {
        // Obtener entradas contables con filtros
        $entries = AccountingEntry::when($request->has('date_from') && !empty($request->date_from), function($q) use ($request) {
                        return $q->where('entry_date', '>=', $request->date_from);
                    })
                    ->when($request->has('date_to') && !empty($request->date_to), function($q) use ($request) {
                        return $q->where('entry_date', '<=', $request->date_to);
                    })
                    ->when($request->has('type') && !empty($request->type), function($q) use ($request) {
                        return $q->where('entry_type', $request->type);
                    })
                    ->orderBy('entry_date', 'desc')
                    ->paginate(15);
        
        // Calcular totales
        $totalIngresos = AccountingEntry::where('entry_type', AccountingEntry::ENTRY_TYPE_INGRESO)
                                    ->when($request->has('date_from') && !empty($request->date_from), function($q) use ($request) {
                                        return $q->where('entry_date', '>=', $request->date_from);
                                    })
                                    ->when($request->has('date_to') && !empty($request->date_to), function($q) use ($request) {
                                        return $q->where('entry_date', '<=', $request->date_to);
                                    })
                                    ->sum('amount');
        
        $totalGastos = AccountingEntry::where('entry_type', AccountingEntry::ENTRY_TYPE_GASTO)
                                    ->when($request->has('date_from') && !empty($request->date_from), function($q) use ($request) {
                                        return $q->where('entry_date', '>=', $request->date_from);
                                    })
                                    ->when($request->has('date_to') && !empty($request->date_to), function($q) use ($request) {
                                        return $q->where('entry_date', '<=', $request->date_to);
                                    })
                                    ->sum('amount');

        return view('pymes.contabilidad.index', compact('entries', 'totalIngresos', 'totalGastos'));
    }

    /**
     * Mostrar formulario para crear entrada contable
     */
    public function contabilidadCreate()
    {
        // Obtener clientes tipo PYME
        $clientes = User::where('level', 'user')
                      ->whereNotNull('business_name')
                      ->orderBy('business_name')
                      ->get();
                      
        // Obtener créditos activos
        $creditos = Credit::where('status', 'inprogress')
                       ->get();
                       
        return view('pymes.contabilidad.create', compact('clientes', 'creditos'));
    }

    /**
     * Almacenar una nueva entrada contable
     */
    public function contabilidadStore(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'entry_type' => 'required|in:ingreso,gasto,ajuste',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:100',
            'category' => 'required|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'credit_id' => 'nullable|exists:credits,id',
            'user_id' => 'nullable|exists:users,id',
            'accounting_account' => 'nullable|string|max:50',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $entry = new AccountingEntry();
        $entry->entry_date = $request->entry_date;
        $entry->description = $request->description;
        $entry->entry_type = $request->entry_type;
        $entry->amount = $request->amount;
        $entry->reference = $request->reference;
        $entry->category = $request->category;
        $entry->subcategory = $request->subcategory;
        $entry->credit_id = $request->credit_id;
        $entry->user_id = $request->user_id;
        $entry->accounting_account = $request->accounting_account;
        $entry->status = $request->status;
        $entry->notes = $request->notes;
        $entry->created_by = auth()->id();
        
        // Si se ha subido un archivo adjunto, guardarlo
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('documents/contabilidad', 'public');
            $entry->attachment_path = $path;
        }
        
        $entry->save();

        return redirect()->route('pymes.contabilidad')->with('success', 'Entrada contable registrada correctamente');
    }

    /**
     * Mostrar reportes
     */
    public function reportes()
    {
        // Obtener reportes disponibles
        $reportes = Report::with(['creator'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('pymes.reportes.index', compact('reportes'));
    }

    /**
     * Mostrar formulario para crear reporte
     */
    public function reportesCreate()
    {
        return view('pymes.reportes.create');
    }

    /**
     * Almacenar un nuevo reporte
     */
    public function reportesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_type' => 'required|in:portfolio,collector,financial,operational,regulatory,custom',
            'query_string' => 'nullable|string',
            'parameters' => 'nullable|json',
            'output_format' => 'required|in:pdf,excel,csv,html,json',
            'currency' => 'required|string',
            'time_period' => 'required|in:day,month,custom',
            'collector_filter' => 'required|in:all,specific',
            'specific_collectors' => 'required_if:collector_filter,specific|array',
            'status' => 'required|in:active,inactive',
            'is_public' => 'nullable|boolean',
            'schedule' => 'nullable|string|max:100',
            'recipients' => 'nullable|json',
        ]);

        $reporte = new Report();
        $reporte->name = $request->name;
        $reporte->description = $request->description;
        $reporte->report_type = $request->report_type;
        $reporte->query_string = $request->query_string;
        
        // Configuración de parámetros básicos y adicionales
        $defaultParams = [
            'currency' => $request->currency,
            'time_period' => $request->time_period,
            'collector_filter' => $request->collector_filter
        ];
        
        // Agregar colectores específicos si se seleccionaron
        if ($request->collector_filter == 'specific' && !empty($request->specific_collectors)) {
            $defaultParams['specific_collectors'] = $request->specific_collectors;
        }
        
        // Combinar con parámetros adicionales si existen
        $additionalParams = $request->parameters ? json_decode($request->parameters, true) : [];
        $allParams = array_merge($defaultParams, $additionalParams);
        
        $reporte->parameters = $allParams;
        $reporte->output_format = $request->output_format;
        $reporte->created_by = auth()->id();
        $reporte->is_public = $request->has('is_public');
        $reporte->schedule = $request->schedule;
        $reporte->recipients = $request->recipients ? json_decode($request->recipients, true) : null;
        $reporte->status = $request->status;
        $reporte->save();

        return redirect()->route('pymes.reportes')->with('success', 'Reporte creado correctamente');
    }

    /**
     * Mostrar detalles de un reporte
     */
    public function reportesShow($id)
    {
        $reporte = Report::with(['creator', 'executions'])
                      ->findOrFail($id);
                      
        return view('pymes.reportes.show', compact('reporte'));
    }

    /**
     * Ejecutar un reporte
     */
    public function reportesExecute($id, Request $request)
    {
        // Obtener el reporte
        $reporte = Report::findOrFail($id);
        
        // Crear registro de ejecución
        $ejecucion = new \App\Models\ReportExecution();
        $ejecucion->report_id = $reporte->id;
        $ejecucion->executed_by = auth()->id();
        $ejecucion->execution_date = now();
        $ejecucion->status = 'in_progress';
        $ejecucion->save();
        
        try {
            // Tiempo de inicio para calcular duración
            $startTime = microtime(true);
            
            // Obtener parámetros base del reporte
            $parameters = $reporte->parameters ?? [];
            
            // Combinar con parámetros de la solicitud si existen
            if ($request->has('execution_params')) {
                $requestParams = json_decode($request->execution_params, true);
                $parameters = array_merge($parameters, $requestParams);
            }
            
            // Establecer parámetros de fecha según el periodo
            $timePeriod = $parameters['time_period'] ?? 'day';
            
            if ($timePeriod == 'day') {
                $dateFrom = $request->date ?? date('Y-m-d');
                $dateTo = $request->date ?? date('Y-m-d');
            } elseif ($timePeriod == 'month') {
                $yearMonth = $request->year_month ?? date('Y-m');
                $dateFrom = $yearMonth . '-01';
                $dateTo = date('Y-m-t', strtotime($dateFrom));
            } else {
                // Periodo personalizado
                $dateFrom = $request->date_from ?? date('Y-m-d');
                $dateTo = $request->date_to ?? date('Y-m-d');
            }
            
            // Actualizar parámetros con fechas
            $parameters['date_from'] = $dateFrom;
            $parameters['date_to'] = $dateTo;
            
            // Guardar parámetros usados
            $ejecucion->parameters_used = $parameters;
            $ejecucion->save();
            
            // Aquí se procesaría la consulta SQL
            $query = $reporte->query_string;
            
            // Reemplazar parámetros en la consulta
            foreach ($parameters as $key => $value) {
                // Si es un array (como en specific_collectors), convertirlo a cadena
                if (is_array($value)) {
                    if ($key == 'specific_collectors') {
                        $value = implode(',', $value);
                    } else {
                        $value = json_encode($value);
                    }
                }
                $query = str_replace('{'.$key.'}', $value, $query);
            }
            
            // Ejecutar consulta y generar el reporte
            // Por ahora, simularemos la ejecución
            
            // Generar nombre de archivo para el resultado
            $filename = 'report_' . $reporte->id . '_' . date('YmdHis') . '.' . $reporte->output_format;
            $filepath = 'reports/' . $filename;
            
            // En una implementación real, aquí se generaría el archivo del reporte
            // y se guardaría en el sistema de archivos
            
            // Actualizar el registro de ejecución
            $executionTime = microtime(true) - $startTime;
            $ejecucion->status = 'success';
            $ejecucion->execution_time = $executionTime;
            $ejecucion->result_file_path = $filepath;
            $ejecucion->save();
            
            // Actualizar la fecha de última ejecución del reporte
            $reporte->last_run_at = now();
            $reporte->save();
            
            return redirect()->route('pymes.reportes.show', $reporte->id)
                ->with('success', 'Reporte ejecutado correctamente');
                
        } catch (\Exception $e) {
            // En caso de error, actualizar el registro con el mensaje de error
            $ejecucion->status = 'failed';
            $ejecucion->error_message = $e->getMessage();
            $ejecucion->save();
            
            return redirect()->route('pymes.reportes.show', $reporte->id)
                ->with('error', 'Error al ejecutar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar cobranza
     */
    public function cobranza()
    {
        // Obtener acciones de cobranza
        $acciones = CollectionAction::with(['client', 'agent', 'credit'])
                                  ->orderBy('scheduled_date', 'desc')
                                  ->paginate(10);

        // Obtener acuerdos de pago
        $acuerdos = PaymentAgreement::with(['client', 'agent'])
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10);

        return view('pymes.cobranza.index', compact('acciones', 'acuerdos'));
    }

    /**
     * Mostrar formulario para crear acción de cobranza
     */
    public function cobranzaAccionesCreate()
    {
        $clientes = User::where('level', 'user')
                    ->whereNotNull('business_name')
                    ->orderBy('business_name')
                    ->get();
                    
        $creditos = Credit::where('status', 'inprogress')
                       ->get();
                       
        $agentes = User::where('level', 'agent')
                    ->orderBy('name')
                    ->get();
                    
        return view('pymes.cobranza.acciones.create', compact('clientes', 'creditos', 'agentes'));
    }

    /**
     * Mostrar formulario para crear acuerdo de pago
     */
    public function cobranzaAcuerdosCreate()
    {
        $clientes = User::where('level', 'user')
                    ->whereNotNull('business_name')
                    ->orderBy('business_name')
                    ->get();
                    
        $agentes = User::where('level', 'agent')
                    ->orderBy('name')
                    ->get();
                    
        return view('pymes.cobranza.acuerdos.create', compact('clientes', 'agentes'));
    }

    /**
     * Mostrar auditoría
     */
    public function auditoria()
    {
        // Aquí se mostrarían los logs de auditoría
        return view('pymes.auditoria.index');
    }

    /**
     * Almacenar una nueva garantía
     */
    public function garantiasStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'credit_id' => 'nullable|exists:credits,id',
            'type' => 'required|string|max:50',
            'description' => 'required|string',
            'value' => 'required|numeric|min:0',
            'status' => 'required|string|max:20',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string',
        ]);

        $garantia = new Collateral();
        $garantia->user_id = $request->user_id;
        $garantia->credit_id = $request->credit_id;
        $garantia->type = $request->type;
        $garantia->description = $request->description;
        $garantia->value = $request->value;
        $garantia->status = $request->status;
        $garantia->notes = $request->notes;
        
        // Si se ha subido un documento, guardarlo
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('documents/garantias', 'public');
            $garantia->document_path = $path;
        }
        
        // Si el estado es activo, marcar como verificado por el usuario actual
        if ($request->status == 'active') {
            $garantia->verified_by = auth()->id();
            $garantia->verification_date = now();
        }
        
        $garantia->save();

        return redirect()->route('pymes.garantias')->with('success', 'Garantía registrada correctamente');
    }

    /**
     * Actualizar garantía existente
     */
    public function garantiasUpdate(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'credit_id' => 'nullable|exists:credits,id',
            'type' => 'required|string|max:50',
            'description' => 'required|string',
            'value' => 'required|numeric|min:0',
            'status' => 'required|string|max:20',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'notes' => 'nullable|string',
        ]);

        $garantia = Collateral::findOrFail($id);
        $garantia->user_id = $request->user_id;
        $garantia->credit_id = $request->credit_id;
        $garantia->type = $request->type;
        $garantia->description = $request->description;
        $garantia->value = $request->value;
        $garantia->status = $request->status;
        $garantia->notes = $request->notes;
        
        // Si se ha subido un nuevo documento, guardarlo
        if ($request->hasFile('document')) {
            // Eliminar documento anterior si existe
            if ($garantia->document_path && Storage::disk('public')->exists($garantia->document_path)) {
                Storage::disk('public')->delete($garantia->document_path);
            }
            
            $path = $request->file('document')->store('documents/garantias', 'public');
            $garantia->document_path = $path;
        }
        
        // Si el estado es activo y no estaba verificado, marcar como verificado por el usuario actual
        if ($request->status == 'active' && !$garantia->verified_by) {
            $garantia->verified_by = auth()->id();
            $garantia->verification_date = now();
        }
        
        $garantia->save();

        return redirect()->route('pymes.garantias.show', $garantia->id)->with('success', 'Garantía actualizada correctamente');
    }

    /**
     * Almacenar un nuevo estado financiero
     */
    public function storeFinancialStatement(Request $request)
    {
        $request->validate([
            'loan_application_id' => 'required|exists:loan_applications,id',
            'statement_type' => 'required|in:balance,income,cashflow',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'statement_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'notes' => 'nullable|string'
        ]);

        $solicitud = LoanApplication::findOrFail($request->loan_application_id);
        
        // Guardar el archivo
        $path = $request->file('statement_file')->store('documents/financial-statements', 'public');
        
        // Crear el estado financiero
        $statement = new FinancialStatement([
            'loan_application_id' => $solicitud->id,
            'user_id' => $solicitud->client_id,
            'statement_type' => $request->statement_type,
            'statement_date' => now(),
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'file_path' => $path,
            'notes' => $request->notes,
            'data' => [] // Se llenará cuando se procese el archivo
        ]);
        
        $statement->save();

        return redirect()
            ->route('pymes.analisis.create', $solicitud->id)
            ->with('success', 'Estado financiero registrado correctamente.');
    }

    /**
     * Validar un estado financiero
     */
    public function validateFinancialStatement($id)
    {
        $statement = FinancialStatement::findOrFail($id);
        
        $statement->is_validated = true;
        $statement->validated_by = auth()->id();
        $statement->validation_date = now();
        $statement->save();

        return redirect()
            ->back()
            ->with('success', 'Estado financiero validado correctamente.');
    }

    /**
     * Almacenar un nuevo análisis de crédito
     */
    public function analisisStore(Request $request)
    {
        $request->validate([
            'loan_application_id' => 'required|exists:loan_applications,id',
            'qualitative_factors' => 'required|array',
            'risk_level' => 'required|in:very_low,low,medium,high,very_high',
            'recommendation' => 'required|in:approve,reject,review',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function() use ($request) {
                // Obtener la solicitud
                $solicitud = LoanApplication::findOrFail($request->loan_application_id);

                // Crear el scoring
                $scoring = new CreditScoring();
                $scoring->loan_application_id = $solicitud->id;
                $scoring->user_id = $solicitud->client_id;
                
                // Calcular score basado en factores cualitativos
                $qualitativeScore = $this->calculateQualitativeScore($request->qualitative_factors);
                $scoring->score = $qualitativeScore;
                
                $scoring->risk_level = $request->risk_level;
                $scoring->scoring_model = 'qualitative_analysis';
                $scoring->financial_indicators = '{}';
                $scoring->qualitative_factors = json_encode($request->qualitative_factors);
                $scoring->external_bureau_data = '{}';
                $scoring->recommendation = $request->recommendation;
                $scoring->notes = $request->notes;
                $scoring->analyst_id = auth()->id();
                $scoring->calculated_by = auth()->id();
                $scoring->calculation_date = now();
                $scoring->save();

                // Actualizar el estado de la solicitud
                $solicitud->status = 'analyzed';
                $solicitud->analyst_id = auth()->id();
                $solicitud->save();

                // Si la recomendación es aprobar, actualizar el estado
                if ($request->recommendation === 'approve') {
                    $solicitud->status = 'approved';
                    $solicitud->approved_by = auth()->id();
                    $solicitud->approval_date = now();
                    $solicitud->save();

                    // Crear el crédito
                    $credit = new Credit();
                    $credit->client_id = $solicitud->client_id;
                    $credit->id_user = $solicitud->client_id;
                    $credit->credit_number = $creditNumber;
                    $credit->amount = $solicitud->amount_requested;
                    $credit->amount_approved = $solicitud->amount_requested;
                    $credit->payment_number = $solicitud->term_months;
                    $credit->interest_rate = $solicitud->interest_rate;
                    $credit->payment_frequency = 'monthly';
                    $credit->first_payment_date = now()->addMonth();
                    $credit->status = 'active';
                    $credit->approved_by = auth()->id();
                    $credit->approved_at = now();
                    $credit->save();

                    // Actualizar la solicitud con el ID del crédito
                    $solicitud->credit_id = $credit->id;
                    $solicitud->save();

                    // Generar plan de pagos
                    $credit->generatePaymentSchedule();
                }
            });

            return redirect()
                ->route('pymes.solicitudes.show', $request->loan_application_id)
                ->with('success', 'Análisis de crédito registrado correctamente' . 
                    ($request->recommendation === 'approve' ? ' y solicitud aprobada' : ''));

        } catch (\Exception $e) {
            \Log::error('Error al guardar análisis: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error al guardar el análisis: ' . $e->getMessage());
        }
    }

    /**
     * Calcular score basado en factores cualitativos
     */
    private function calculateQualitativeScore($factors)
    {
        $scores = [
            'excellent' => 100,
            'very_stable' => 100,
            'leader' => 100,
            'good' => 80,
            'stable' => 80,
            'strong' => 80,
            'average' => 60,
            'variable' => 60,
            'moderate' => 60,
            'limited' => 40,
            'weak' => 40,
            'unstable' => 20,
            'poor' => 20,
            'none' => 0
        ];

        $total = 0;
        $count = 0;

        foreach ($factors as $factor => $value) {
            if (isset($scores[$value])) {
                $total += $scores[$value];
                $count++;
            }
        }

        return $count > 0 ? round($total / $count) : 0;
    }
}
