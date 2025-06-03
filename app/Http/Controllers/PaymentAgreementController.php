<?php

namespace App\Http\Controllers;

use App\Models\PaymentAgreement;
use App\Models\Credit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentAgreementController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar listado de acuerdos de pago
     */
    public function index(Request $request)
    {
        // Verificar permiso
        if (!Auth::user()->can('view-payment-agreements')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $query = PaymentAgreement::with(['credit.user', 'approver']);
        
        // Aplicar filtros si existen
        if ($request->has('credit_id') && !empty($request->credit_id)) {
            $query->where('credit_id', $request->credit_id);
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->where('agreement_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->where('agreement_date', '<=', $request->date_to);
        }
        
        if ($request->has('payment_date_from') && !empty($request->payment_date_from)) {
            $query->where('payment_date', '>=', $request->payment_date_from);
        }
        
        if ($request->has('payment_date_to') && !empty($request->payment_date_to)) {
            $query->where('payment_date', '<=', $request->payment_date_to);
        }
        
        $agreements = $query->orderBy('agreement_date', 'desc')->paginate(15);
        $credits = Credit::where('status', 'active')->with('user')->get();
        
        return view('pymes.cobranza.acuerdos.index', compact('agreements', 'credits'));
    }

    /**
     * Mostrar formulario para crear un nuevo acuerdo
     */
    public function create(Request $request)
    {
        // Verificar permiso
        if (!Auth::user()->can('create-payment-agreement')) {
            return redirect()->route('collection.agreements.index')->with('error', 'No tienes permisos para crear acuerdos de pago.');
        }
        
        $credit = null;
        if ($request->has('credit_id') && !empty($request->credit_id)) {
            $credit = Credit::with('user')->findOrFail($request->credit_id);
        }
        
        return view('pymes.cobranza.acuerdos.create', compact('credit'));
    }

    /**
     * Almacenar un nuevo acuerdo
     */
    public function store(Request $request)
    {
        // Verificar permiso
        if (!Auth::user()->can('create-payment-agreement')) {
            return redirect()->route('collection.agreements.index')->with('error', 'No tienes permisos para crear acuerdos de pago.');
        }
        
        $request->validate([
            'credit_id' => 'required|exists:credits,id',
            'agreement_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:agreement_date',
            'status' => 'required|in:pending,completed,cancelled,partial',
            'description' => 'required|string',
            'payment_method' => 'nullable|in:cash,bank_transfer,check,debit_card,credit_card,mobile_payment,other',
            'approved_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $agreement = new PaymentAgreement();
            $agreement->credit_id = $request->credit_id;
            $agreement->agreement_date = $request->agreement_date;
            $agreement->amount = $request->amount;
            $agreement->payment_date = $request->payment_date;
            $agreement->status = $request->status;
            $agreement->description = $request->description;
            $agreement->payment_method = $request->payment_method;
            $agreement->approved_by = $request->approved_by;
            $agreement->notes = $request->notes;
            $agreement->created_by = Auth::id();
            $agreement->save();
            
            DB::commit();
            
            if ($request->has('redirect_to') && !empty($request->redirect_to)) {
                return redirect($request->redirect_to)->with('success', 'Acuerdo de pago creado correctamente');
            }
            
            return redirect()->route('collection.agreements.index')->with('success', 'Acuerdo de pago creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el acuerdo de pago: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mostrar detalle de un acuerdo
     */
    public function show($id)
    {
        // Verificar permiso
        if (!Auth::user()->can('view-payment-agreements')) {
            return redirect()->route('collection.agreements.index')->with('error', 'No tienes permisos para ver detalles de acuerdos de pago.');
        }
        
        $agreement = PaymentAgreement::with(['credit.user', 'approver', 'creator'])->findOrFail($id);
        
        return view('pymes.cobranza.acuerdos.show', compact('agreement'));
    }

    /**
     * Mostrar formulario para editar un acuerdo
     */
    public function edit($id)
    {
        // Verificar permiso
        if (!Auth::user()->can('edit-payment-agreement')) {
            return redirect()->route('collection.agreements.index')->with('error', 'No tienes permisos para editar acuerdos de pago.');
        }
        
        $agreement = PaymentAgreement::with('credit.user')->findOrFail($id);
        
        return view('pymes.cobranza.acuerdos.edit', compact('agreement'));
    }

    /**
     * Actualizar un acuerdo existente
     */
    public function update(Request $request, $id)
    {
        // Verificar permiso
        if (!Auth::user()->can('edit-payment-agreement')) {
            return redirect()->route('collection.agreements.index')->with('error', 'No tienes permisos para editar acuerdos de pago.');
        }
        
        $agreement = PaymentAgreement::findOrFail($id);
        
        $request->validate([
            'agreement_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:agreement_date',
            'status' => 'required|in:pending,completed,cancelled,partial',
            'description' => 'required|string',
            'payment_method' => 'nullable|in:cash,bank_transfer,check,debit_card,credit_card,mobile_payment,other',
            'approved_by' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $agreement->agreement_date = $request->agreement_date;
            $agreement->amount = $request->amount;
            $agreement->payment_date = $request->payment_date;
            $agreement->status = $request->status;
            $agreement->description = $request->description;
            $agreement->payment_method = $request->payment_method;
            $agreement->approved_by = $request->approved_by;
            $agreement->notes = $request->notes;
            $agreement->updated_by = Auth::id();
            $agreement->save();
            
            DB::commit();
            return redirect()->route('collection.agreements.index')->with('success', 'Acuerdo de pago actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el acuerdo de pago: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Eliminar un acuerdo
     */
    public function destroy($id)
    {
        // Verificar permiso
        if (!Auth::user()->can('delete-payment-agreement')) {
            return redirect()->route('collection.agreements.index')->with('error', 'No tienes permisos para eliminar acuerdos de pago.');
        }
        
        $agreement = PaymentAgreement::findOrFail($id);
        
        try {
            $agreement->delete();
            return redirect()->route('collection.agreements.index')->with('success', 'Acuerdo de pago eliminado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el acuerdo de pago: ' . $e->getMessage());
        }
    }
} 