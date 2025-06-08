<?php

namespace App\Http\Controllers;

use App\db_agent_has_user;
use App\db_credit;
use App\db_not_pay;
use App\db_summary;
use App\db_supervisor_has_agent;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->id = Auth::user()->id;
            
            // Verificar si el usuario es supervisor o admin (pueden siempre acceder)
            if (Auth::user()->role == 'supervisor' || Auth::user()->role == 'admin' || Auth::user()->role == 'superadmin') {
                return $next($request);
            }
            
            // Verificar si existe la relación en cualquiera de las bases de datos
            try {
                // Primero intentamos con la conexión por defecto
                $hasRelation = false;
                
                try {
                    $hasRelation = db_supervisor_has_agent::where('id_user_agent', Auth::id())->exists();
                } catch (\Exception $e) {
                    // Si falla, es posible que el modelo esté usando otra conexión
                    \Log::warning('Error al verificar en la primera base de datos: ' . $e->getMessage());
                }
                
                // Si no encontramos la relación, intentamos con una consulta directa a sistema_prestamos
                if (!$hasRelation) {
                    try {
                        // Configurar temporalmente la conexión a sistema_prestamos
                        \Config::set('database.connections.sistema_prestamos', [
                            'driver' => 'mysql',
                            'host' => env('DB_HOST', '127.0.0.1'),
                            'port' => env('DB_PORT', '3306'),
                            'database' => 'sistema_prestamos',
                            'username' => env('DB_USERNAME', 'root'),
                            'password' => env('DB_PASSWORD', ''),
                            'charset' => 'utf8mb4',
                            'collation' => 'utf8mb4_unicode_ci',
                            'prefix' => '',
                            'strict' => true,
                            'engine' => null,
                        ]);
                        
                        $hasRelation = \DB::connection('sistema_prestamos')
                            ->table('agent_has_supervisor')
                            ->where('id_user_agent', Auth::id())
                            ->exists();
                    } catch (\Exception $e) {
                        \Log::warning('Error al verificar en sistema_prestamos: ' . $e->getMessage());
                    }
                }
                
                // También probamos con sistema_prestamos
                if (!$hasRelation) {
                    try {
                        // Configurar temporalmente la conexión a sistema_prestamos
                        \Config::set('database.connections.sistema_prestamos', [
                            'driver' => 'mysql',
                            'host' => env('DB_HOST', '127.0.0.1'),
                            'port' => env('DB_PORT', '3306'),
                            'database' => 'sistema_prestamos',
                            'username' => env('DB_USERNAME', 'root'),
                            'password' => env('DB_PASSWORD', ''),
                            'charset' => 'utf8mb4',
                            'collation' => 'utf8mb4_unicode_ci',
                            'prefix' => '',
                            'strict' => true,
                            'engine' => null,
                        ]);
                        
                        $hasRelation = \DB::connection('sistema_prestamos')
                            ->table('agent_has_supervisor')
                            ->where('id_user_agent', Auth::id())
                            ->exists();
                    } catch (\Exception $e) {
                        \Log::warning('Error al verificar en sistema_prestamos: ' . $e->getMessage());
                    }
                }
                
                // Si no existe en ninguna de las bases de datos, crear una relación
                if (!$hasRelation) {
                    // Buscar un supervisor en cualquiera de las bases de datos
                    $supervisorId = null;
                    $walletId = null;
                    $dbConnection = null;
                    
                    // Primero en la conexión por defecto
                    try {
                        $supervisorId = User::where('role', 'supervisor')->value('id');
                        $walletId = \DB::table('wallet')->first()->id ?? 1;
                        $dbConnection = 'default';
                    } catch (\Exception $e) {
                        \Log::info('No se encontró supervisor en la conexión por defecto');
                    }
                    
                    // Luego en sistema_prestamos
                    if (!$supervisorId) {
                        try {
                            $supervisorId = \DB::connection('sistema_prestamos')
                                ->table('users')
                                ->where('role', 'supervisor')
                                ->value('id');
                                
                            $walletId = \DB::connection('sistema_prestamos')
                                ->table('wallet')
                                ->first()->id ?? 1;
                                
                            $dbConnection = 'sistema_prestamos';
                        } catch (\Exception $e) {
                            \Log::info('No se encontró supervisor en sistema_prestamos');
                        }
                    }
                    
                    // Finalmente en sistema_prestamos
                    if (!$supervisorId) {
                        try {
                            $supervisorId = \DB::connection('sistema_prestamos')
                                ->table('users')
                                ->where('role', 'supervisor')
                                ->value('id');
                                
                            $walletId = \DB::connection('sistema_prestamos')
                                ->table('wallet')
                                ->first()->id ?? 1;
                                
                            $dbConnection = 'sistema_prestamos';
                        } catch (\Exception $e) {
                            \Log::info('No se encontró supervisor en sistema_prestamos');
                        }
                    }
                    
                    // Si encontramos un supervisor, creamos la relación
                    if ($supervisorId && $walletId && $dbConnection) {
                        try {
                            if ($dbConnection === 'default') {
                                db_supervisor_has_agent::insert([
                                    'id_user_agent' => Auth::id(),
                                    'id_supervisor' => $supervisorId,
                                    'base' => 0.00,
                                    'id_wallet' => $walletId,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            } else {
                                \DB::connection($dbConnection)
                                    ->table('agent_has_supervisor')
                                    ->insert([
                                        'id_user_agent' => Auth::id(),
                                        'id_supervisor' => $supervisorId,
                                        'base' => 0.00,
                                        'id_wallet' => $walletId,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                            }
                            
                            // Ahora que existe la relación, continuar
                            return $next($request);
                        } catch (\Exception $e) {
                            \Log::error('Error al crear la relación: ' . $e->getMessage());
                        }
                    }
                    
                    // Si no hay supervisores, mostrar mensaje más amigable
                    return view('payment.error', [
                        'title' => 'Acceso no autorizado',
                        'message' => 'Tu usuario no está asignado a ningún supervisor. Por favor, contacta al administrador.'
                    ]);
                }
                
            return $next($request);
            } catch (\Exception $e) {
                \Log::error('Error general al verificar relación: ' . $e->getMessage());
                return view('payment.error', [
                    'title' => 'Error de configuración',
                    'message' => 'Hay un problema con la configuración de la base de datos. Por favor, contacta al administrador.'
                ]);
            }
        });
    }

    public function index()
    {

        $data_user = db_credit::where('credit.id_agent', Auth::id())
            ->join('users', 'credit.id_user', '=', 'users.id')
            ->select('credit.*', 'users.id as id_user',
                'users.name', 'users.last_name'
            )
            ->get();

        foreach ($data_user as $data) {
            if (db_credit::where('id_user', $data->id_user)->where('id_agent', Auth::id())->exists()) {

                $data->setAttribute('credit_id', $data->id);
                $data->setAttribute('amount_neto', ($data->amount_neto) + ($data->amount_neto * $data->utility));
                $data->setAttribute('positive', $data->amount_neto - (db_summary::where('id_credit', $data->id)
                        ->where('id_agent', Auth::id())
                        ->sum('amount')));
                $data->setAttribute('payment_current', db_summary::where('id_credit', $data->id)->count());
            }

        }
        $data = array(
            'clients' => $data_user
        );

        return view('payment.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $amount = $request->amount;
        $credit_id = $request->credit_id;

        $redirect_error = '/payment?msg=Fields_Null&status=error';
        if (!isset($credit_id)) {
            return redirect($redirect_error);
        };
        if (!isset($amount)) {
            return redirect($redirect_error);
        };

        $values = array(
            'created_at' => Carbon::now(),
            'amount' => $amount,
            'id_agent' => Auth::id(),
            'id_credit' => $credit_id,
        );

        db_summary::insert($values);

        return redirect('');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if (!db_credit::where('id', $id)->exists()) {
            return 'No existe credido';
        } else {
            $data_tmp = db_credit::where('id', $id)->first();
            if (Auth::id() != $data_tmp->id_agent) {
                return 'No tienes permisos';
            }
        }

        $data = db_credit::find($id);
        $data->user = User::find($data->id_user);
        $tmp_amount = db_summary::where('id_credit', $id)
            ->where('id_agent', Auth::id())
            ->sum('amount');
        $amount_neto = $data->amount_neto;
        $amount_neto += floatval($amount_neto * $data->utility);
        $data->amount_neto = $amount_neto;


//        dd([$amount_neto,$tmp_amount]);

        $tmp_quote = round(floatval(($amount_neto / $data->payment_number)), 2);
        $tmp_rest = round(floatval($amount_neto - $tmp_amount), 2);

        $data->credit_data = array(
            'positive' => $tmp_amount,
            'rest' => round(floatval($amount_neto - $tmp_amount), 2),
            'payment_done' => db_summary::where('id_credit', $id)->count(),
            'payment_quote' => ($tmp_rest > $tmp_quote) ? $tmp_rest : $tmp_quote
    );


        if ($request->input('format') === 'json') {
            $response = array(
                'status' => 'success',
                'data' => $data,
                'code' => 0
            );
            return response()->json($response);
        } else {
            return view('payment.create', $data);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $id_credit = $request->id_credit;

        if (!isset($id_credit)) {
            return 'ID cretido';
        };

        $values = array(
            'created_at' => Carbon::now(),
            'id_credit' => $id_credit,
            'id_user' => $id
        );

        db_not_pay::insert($values);

        if ($request->ajax) {
            $response = array(
                'status' => 'success'
            );
            return response()->json($response);
        } else {
            return redirect('route');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
