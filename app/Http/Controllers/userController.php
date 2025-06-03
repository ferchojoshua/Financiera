<?php

namespace App\Http\Controllers;

use App\db_agent_has_user;
use App\db_credit;
use App\db_summary;
use App\db_supervisor_has_agent;
use App\db_wallet;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class userController extends Controller
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
            
            try {
                // Verificar si la tabla agent_has_supervisor existe
                if (Schema::hasTable('agent_has_supervisor')) {
                    // Verificar si existe la relación entre agente y supervisor
                    $exists = DB::table('agent_has_supervisor')
                        ->where('id_user_agent', Auth::id())
                        ->exists();
                        
                    if (!$exists) {
                        $this->createAgentSupervisorRelation();
                    }
                } else {
                    // Si la tabla no existe, ejecutar la migración correspondiente
                    try {
                        Artisan::call('migrate', [
                            '--path' => 'database/migrations/2025_06_03_000001_fix_agent_has_supervisor_table.php',
                            '--force' => true
                        ]);
                        
                        // Después de migrar, crear la relación
                        $this->createAgentSupervisorRelation();
                        
                        Log::info('Se ejecutó la migración para crear la tabla agent_has_supervisor');
                    } catch (\Exception $e) {
                        Log::error('Error al ejecutar la migración: ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error en middleware de userController: ' . $e->getMessage());
            }
            
            return $next($request);
        });
    }
    
    /**
     * Método para crear la relación entre agente y supervisor
     */
    private function createAgentSupervisorRelation()
    {
        try {
            // Buscar un supervisor existente
            $supervisor = DB::table('users')
                ->where('role', 'supervisor')
                ->orWhere('level', 'supervisor')
                ->first();
            
            if (!$supervisor) {
                // Crear un supervisor predeterminado si no existe ninguno
                $supervisorId = DB::table('users')->insertGetId([
                    'name' => "Supervisor Predeterminado",
                    'email' => "supervisor_" . time() . "@sistema.com",
                    'password' => bcrypt("supervisor123"),
                    'level' => "supervisor",
                    'role' => "supervisor",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $supervisorId = $supervisor->id;
            }
            
            // Buscar una cartera existente
            $wallet = DB::table('wallet')->first();
            
            if (!$wallet) {
                // Crear una cartera predeterminada si no existe ninguna
                $walletId = DB::table('wallet')->insertGetId([
                    'name' => "Cartera Predeterminada",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $walletId = $wallet->id;
            }
            
            // Crear la relación entre el agente y el supervisor
            DB::table('agent_has_supervisor')->insert([
                'id_supervisor' => $supervisorId,
                'id_user_agent' => Auth::id(),
                'id_wallet' => $walletId,
                'base' => 5000.00, // Base predeterminada
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Registrar en el log
            Log::info("Se creó automáticamente la relación Supervisor-Agente para el usuario " . Auth::id());
        } catch (\Exception $e) {
            Log::error('Error al crear la relación Supervisor-Agente: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $user_current = Auth::user();

        try {
            $user_has_agent = db_agent_has_user::where('id_agent', Auth::id())
                ->join('users', 'id_client', '=', 'users.id')
                ->get();

            if ($user_current->level === 'admin' || $user_current->role === 'admin' || $user_current->role === 'superadmin') {
                $user_has_agent = db_agent_has_user::join('users', 'id_client', '=', 'users.id')
                    ->get();
            }

            foreach ($user_has_agent as $user) {
                if (db_credit::where('id_user', $user->id)->exists()) {
                    $user->closed = db_credit::where('status', 'close')->where('id_user', $user->id)->count();
                    $user->inprogress = db_credit::where('status', 'inprogress')->where('id_user', $user->id)->count();
                    $user->credit_count = db_credit::where('id_user', $user->id)->count();
                    $user->amount_net = db_credit::where('id_user', $user->id)
                        ->where('status', 'inprogress')
                        ->first();

                    $user->summary_net = ($user->amount_net) ? db_summary::where('id_credit', $user->amount_net->id)
                        ->sum('amount') : 0;

                    $tmp_credit = $user->amount_net->amount_neto ?? 0;
                    $tmp_rest = $tmp_credit - $user->summary_net;
                    $user->summary_net = $tmp_rest;

                    if($user->amount_net){
                        $user->gap_credit = $tmp_credit * $user->amount_net->utility;
                    }
                }
            }

            $user_has_agent = array(
                'clients' => $user_has_agent,
            );
            
            return view('client.index', $user_has_agent);
        } catch (\Exception $e) {
            Log::error('Error en userController@index: ' . $e->getMessage());
            return view('client.index', ['clients' => [], 'error' => 'Error al cargar clientes']);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $id = $request->id;
        $data = array(
            'user' => User::find($id),
            'payment_number' => DB::table('payment_number')->orderBy('name', 'asc')->get(),
            'branches' => \App\Models\Branch::where('status', 'active')->orderBy('name', 'asc')->get()
        );
        return view('client.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->level == 'agent') {
            return 'No tienes permisos';
        }
        $name = $request->name;
        $last_name = $request->last_name ?? '';
        $address = $request->address;
        $province = $request->province ?? '';
        $phone = $request->phone;
        $nit = $request->nit_number;
        $utility = $request->utility ?? $request->interest_rate / 100;
        $payment_number = $request->payment_number ?? $request->term;
        $amount = $request->amount ?? $request->approved_amount;
        $lat = $request->lat;
        $lng = $request->lng;
        $branch_id = $request->branch_id;
        
        $email = $request->email;
        $gender = $request->gender;
        $house = $request->house;
        $civil_status = $request->civil_status;
        $spouse_name = $request->spouse_name;
        $spouse_job = $request->spouse_job;
        $spouse_phone = $request->spouse_phone;
        $business_type = $request->business_type;
        $business_time = $request->business_time;
        $sales_good = $request->sales_good;
        $sales_bad = $request->sales_bad;
        $weekly_average = $request->weekly_average;
        $net_profit = $request->net_profit;
        $payment_type = $request->payment_type;

        $redirect_error = '/client?msg=Fields_Null&status=error';
        if (!isset($name)) {
            return redirect($redirect_error);
        };
        if (!isset($address)) {
            return redirect($redirect_error);
        };
        if (!isset($phone)) {
            return redirect($redirect_error);
        };
        if (!isset($nit)) {
            return redirect($redirect_error);
        };
        if (!isset($utility)) {
            return redirect($redirect_error);
        };
        if (!isset($payment_number)) {
            return redirect($redirect_error);
        };
        if (!isset($amount)) {
            return redirect($redirect_error);
        };
        if (!isset($branch_id)) {
            return redirect($redirect_error)->with('error', 'Debe seleccionar una sucursal');
        };

        $base = db_supervisor_has_agent::where('id_user_agent', Auth::id())->first()->base;
        $base_credit = db_credit::whereDate('created_at', Carbon::now()->toDateString())
            ->where('id_agent', Auth::id())
            ->sum('amount_neto');
        $base -= $base_credit;

        if ($amount > $base) {
            return redirect()->back()->with('error', 'No tienes dinero suficiente');
        }

        $values = array(
            'name' => strtoupper($name),
            'last_name' => strtoupper($last_name),
            'email' => $email ?? $nit,
            'level' => 'user',
            'address' => strtoupper($address),
            'province' => strtoupper($province),
            'phone' => $phone,
            'password' => Str::random(5),
            'lat' => $lat,
            'lng' => $lng,
            'nit' => $nit,
            'branch_id' => $branch_id,
            'gender' => $gender,
            'house_type' => $house,
            'civil_status' => $civil_status,
            'spouse_name' => $spouse_name,
            'spouse_job' => $spouse_job,
            'spouse_phone' => $spouse_phone,
            'business_type' => $business_type,
            'business_time' => $business_time,
            'sales_good' => $sales_good,
            'sales_bad' => $sales_bad,
            'weekly_average' => $weekly_average,
            'net_profit' => $net_profit
        );

        if (!User::where('nit', $nit)->exists()) {
            $id = User::insertGetId($values);
        } else {
            $id = User::where('nit', $nit)->first()->id;

            if (db_agent_has_user::where('id_client', $id)->exists()) {
                $agent_data = db_agent_has_user::where('id_client', $id)->first();
                if ($agent_data->id_agent != Auth::id()) {
                    return 'Este usuario ya esta asignado a otro Agente';
                }
            }
            
            User::where('id', $id)->update($values);
        }

        if (!db_agent_has_user::where('id_agent', Auth::id())->where('id_client', $id)->exists()) {
            db_agent_has_user::insert([
                'id_agent' => Auth::id(),
                'id_client' => $id,
                'id_wallet' => db_supervisor_has_agent::where('id_user_agent', Auth::id())->first()->id_wallet]);
        }

        if (db_credit::orderBy('order_list', 'DESC')->first() === null) {
            $last_order = 0;
        } else {
            $last_order = db_credit::orderBy('order_list', 'DESC')->first()->order_list;
        }

        $values = array(
            'created_at' => Carbon::now(),
            'payment_number' => $payment_number,
            'utility' => $utility,
            'amount_neto' => $amount,
            'id_user' => $id,
            'id_agent' => Auth::id(),
            'order_list' => ($last_order) + 1,
            'branch_id' => $branch_id,
            'payment_frequency' => $payment_type ?? 'diario',
            'first_payment_date' => $request->first_payment ?? Carbon::now()->addDay()
        );
        db_credit::insert($values);
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return redirect()->route('client.index')->with('error', 'Cliente no encontrado');
        }
        
        $data = array(
            'user' => $user,
        );
        return view('client.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
