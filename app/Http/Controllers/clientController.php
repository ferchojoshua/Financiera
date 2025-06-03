<?php

namespace App\Http\Controllers;

use App\db_agent_has_user;
use App\db_countries;
use App\db_credit;
use App\db_supervisor_has_agent;
use App\db_wallet;
use App\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtener todos los clientes
        $clients = User::where('level', 'client')->orderBy('created_at', 'desc')->paginate(10);
        
        return view('client.index', ['clients' => $clients]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            // Debug - Mostrar que estamos entrando al método
            echo "<!-- DEBUG: Entrando al método create de ClientController -->";
            
            // Versión simplificada para diagnóstico
            return view('client.create', [
                'branches' => Branch::all(),
                'countries' => db_countries::all(),
            ]);
            
        } catch (\Exception $e) {
            // Log el error y mostrar en HTML para depuración
            echo "<!-- ERROR: " . $e->getMessage() . " -->";
            echo "<!-- En archivo: " . $e->getFile() . " línea: " . $e->getLine() . " -->";
            
            // Log detallado
            \Log::error('Error al cargar la vista de creación de cliente: ' . $e->getMessage());
            \Log::error('Archivo: ' . $e->getFile() . ' en línea ' . $e->getLine());
            \Log::error('Traza: ' . $e->getTraceAsString());
            
            // Retornar error como HTML simple para diagnóstico
            return response()->make(
                '<html><body><h1>Error</h1><p>' . $e->getMessage() . '</p><p>Archivo: ' . 
                $e->getFile() . ' línea: ' . $e->getLine() . '</p></body></html>', 
                500, ['Content-Type' => 'text/html']
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'nit_number' => 'required|string|max:30|unique:users,nit',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        try {
            // Crear nuevo usuario/cliente
            $user = new User();
            $user->name = $request->name;
            $user->nit = $request->nit_number;
            $user->address = $request->address;
            $user->phone = $request->phone;
            $user->level = 'client';
            $user->status = 'good';
            
            // Campos opcionales
            if ($request->has('branch_id') && !empty($request->branch_id)) $user->branch_id = $request->branch_id;
            if ($request->has('email') && !empty($request->email)) $user->email = $request->email;
            if ($request->has('province') && !empty($request->province)) $user->province = $request->province;
            if ($request->has('city') && !empty($request->city)) $user->city = $request->city;
            if ($request->has('last_name') && !empty($request->last_name)) $user->last_name = $request->last_name;
            
            // Datos adicionales si se proporcionan
            if ($request->has('gender') && !empty($request->gender)) $user->gender = $request->gender;
            if ($request->has('house') && !empty($request->house)) $user->house_type = $request->house;
            if ($request->has('civil_status') && !empty($request->civil_status)) $user->civil_status = $request->civil_status;
            if ($request->has('spouse_name') && !empty($request->spouse_name)) $user->spouse_name = $request->spouse_name;
            if ($request->has('spouse_job') && !empty($request->spouse_job)) $user->spouse_job = $request->spouse_job;
            if ($request->has('spouse_phone') && !empty($request->spouse_phone)) $user->spouse_phone = $request->spouse_phone;
            
            // Datos del negocio
            if ($request->has('business_type') && !empty($request->business_type)) $user->business_type = $request->business_type;
            if ($request->has('business_time') && !empty($request->business_time)) $user->business_time = $request->business_time;
            if ($request->has('sales_good') && !empty($request->sales_good)) $user->sales_good = $request->sales_good;
            if ($request->has('sales_bad') && !empty($request->sales_bad)) $user->sales_bad = $request->sales_bad;
            if ($request->has('weekly_average') && !empty($request->weekly_average)) $user->weekly_average = $request->weekly_average;
            if ($request->has('net_profit') && !empty($request->net_profit)) $user->net_profit = $request->net_profit;
            
            // Coordenadas si están disponibles
            if ($request->has('lat') && !empty($request->lat)) $user->lat = $request->lat;
            if ($request->has('lng') && !empty($request->lng)) $user->lng = $request->lng;
            
            $user->save();
            
            // Si el cliente fue creado exitosamente, y si hay información de préstamo, crear crédito
            if ($request->has('requested_amount') && !empty($request->requested_amount) && 
                $request->has('approved_amount') && !empty($request->approved_amount) &&
                $request->has('interest_rate') && !empty($request->interest_rate)) {
                
                // Crear el crédito asociado al cliente
                $credit = new \App\db_credit();
                $credit->id_user = $user->id;
                $credit->id_agent = Auth::id(); // El usuario actual como agente
                $credit->credit_number = 'CR-' . time(); // Generar número de crédito único
                $credit->utility_rate = $request->interest_rate;
                $credit->amount_requested = $request->requested_amount;
                $credit->amount_approved = $request->approved_amount;
                $credit->status = 'inprogress';
                
                if ($request->has('first_payment')) $credit->first_pay = $request->first_payment;
                if ($request->has('payment_type')) $credit->payment_type = $request->payment_type;
                if ($request->has('term')) $credit->payment_number = $request->term;
                
                $credit->save();
                
                // Si hay una ruta seleccionada, asignar el crédito a esa ruta
                if ($request->has('route') && !empty($request->route)) {
                    $routeCredit = new \App\RouteCredit();
                    $routeCredit->route_id = $request->route;
                    $routeCredit->credit_id = $credit->id;
                    $routeCredit->save();
                }
            }
            
            // Si el cliente fue creado exitosamente, redirigir con mensaje de éxito
            return redirect()->route('client.index')->with('success', 'Cliente creado correctamente');
            
        } catch (\Exception $e) {
            // Si ocurre un error, redirigir con mensaje de error
            return redirect()->back()->with('error', 'Error al crear cliente: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = db_agent_has_user::where('agent_has_client.id_wallet', $id)
            ->join('users', 'agent_has_client.id_client', '=', 'users.id')
            ->join('credit', 'users.id', '=', 'credit.id_user')
            ->select(
                'users.name',
                'users.last_name',
                'users.province',
                'users.status',
                'users.id as id_user',
                DB::raw('COUNT(*) as total_credit')
            )
            ->groupBy('users.id')
            ->get();

        foreach ($data as $datum) {
            $datum->credit_inprogress = db_credit::where('status', 'inprogress')->where('id_user', $datum->id_user)->count();
            $datum->credit_close = db_credit::where('status', 'close')->where('id_user', $datum->id_user)->count();
        }
        $data = array(
            'clients' => $data
        );

        return view('supervisor_client.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::find($id);
        $data = array(
            'user' => $data
        );
        return view('supervisor_client.unique', $data);
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
        $name = $request->name;
        $last_name = $request->last_name;
        $nit = $request->nit_number;
        $address = $request->address;
        $province = $request->province;
        $phone = $request->phone;
        $status = $request->status;

        $values = array(
            'name' => $name,
            'last_name' => $last_name,
            'nit' => $nit,
            'address' => $address,
            'province' => $province,
            'phone' => $phone,
            'status' => $status
        );

        User::where('id', $id)->update($values);
        if (db_agent_has_user::where('id_client', $id)->exists()) {
            $wallet = db_agent_has_user::where('id_client', $id)->first();
            return redirect('supervisor/client/' . $wallet->id_wallet);
        } else {
            return redirect('supervisor/client/');
        }

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

    // Método para generar el reporte de clientes por categoría
    public function report()
    {
        return view('reports.clients.categories');
    }
    
    // Método para generar el reporte de desempeño de clientes
    public function performance()
    {
        return view('reports.clients.performance');
    }
}
