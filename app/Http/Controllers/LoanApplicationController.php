<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CreditType;
use App\Models\LoanApplication;
use App\Models\ClientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LoanApplication::with(['client', 'creditType', 'analyst', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client')) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%')
                  ->orWhere('last_name', 'like', '%' . $request->client . '%')
                  ->orWhere('nit', 'like', '%' . $request->client . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $applications = $query->paginate(10);

        return view('loan_applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $creditTypes = CreditType::orderBy('name')->get();

        return view('loan_applications.create', compact('clients', 'creditTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'credit_type_id' => 'required|exists:credit_types,id',
            'amount_requested' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1',
            'payment_frequency' => 'required|in:daily,weekly,biweekly,monthly',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validar límites de monto según el tipo de crédito
        $creditType = CreditType::findOrFail($request->credit_type_id);
        
        if ($request->amount_requested < $creditType->min_amount || 
            $request->amount_requested > $creditType->max_amount) {
            return back()
                ->withInput()
                ->withErrors(['amount_requested' => 'El monto debe estar entre $' . 
                    number_format($creditType->min_amount, 2) . ' y $' . 
                    number_format($creditType->max_amount, 2)]);
        }

        $application = new LoanApplication($validated);
        $application->status = 'pending';
        $application->created_by = Auth::id();
        $application->save();

        return redirect()
            ->route('loan-applications.show', $application)
            ->with('success', 'Solicitud creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanApplication $application)
    {
        $application->load(['client', 'creditType', 'analyst', 'createdBy', 'approvedBy']);
        return view('loan_applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('error', 'Solo se pueden editar solicitudes pendientes.');
        }

        $clients = Client::orderBy('name')->get();
        $creditTypes = CreditType::orderBy('name')->get();

        return view('loan_applications.edit', compact('application', 'clients', 'creditTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoanApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('error', 'Solo se pueden editar solicitudes pendientes.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'credit_type_id' => 'required|exists:credit_types,id',
            'amount_requested' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1',
            'payment_frequency' => 'required|in:daily,weekly,biweekly,monthly',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validar límites de monto según el tipo de crédito
        $creditType = CreditType::findOrFail($request->credit_type_id);
        
        if ($request->amount_requested < $creditType->min_amount || 
            $request->amount_requested > $creditType->max_amount) {
            return back()
                ->withInput()
                ->withErrors(['amount_requested' => 'El monto debe estar entre $' . 
                    number_format($creditType->min_amount, 2) . ' y $' . 
                    number_format($creditType->max_amount, 2)]);
        }

        $application->update($validated);

        return redirect()
            ->route('loan-applications.show', $application)
            ->with('success', 'Solicitud actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('error', 'Solo se pueden eliminar solicitudes pendientes.');
        }

        if (Auth::user()->cannot('delete', $application)) {
            return back()->with('error', 'No tiene permiso para eliminar esta solicitud.');
        }

        $application->delete();

        return redirect()
            ->route('loan-applications.index')
            ->with('success', 'Solicitud eliminada exitosamente.');
    }

    public function approve(Request $request, LoanApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
        }

        if (Auth::user()->cannot('approve', $application)) {
            return back()->with('error', 'No tiene permiso para aprobar solicitudes.');
        }

        DB::transaction(function() use ($application, $request) {
            // Actualizar la solicitud
            $application->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approval_date' => now(),
                'approval_notes' => $request->notes
            ]);

            // Crear el crédito
            $credit = $application->createCredit();
            
            // Generar el plan de pagos
            $credit->generatePaymentSchedule();
        });

        return redirect()
            ->route('loan-applications.show', $application)
            ->with('success', 'Solicitud aprobada exitosamente.');
    }

    public function reject(Request $request, LoanApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
        }

        if (Auth::user()->cannot('reject', $application)) {
            return back()->with('error', 'No tiene permiso para rechazar solicitudes.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $application->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejection_date' => now(),
            'rejection_reason' => $validated['rejection_reason']
        ]);

        return redirect()
            ->route('loan-applications.show', $application)
            ->with('success', 'Solicitud rechazada exitosamente.');
    }

    /**
     * Subir documento a una solicitud
     */
    public function uploadDocument(Request $request, LoanApplication $application)
    {
        $request->validate([
            'document_type' => 'required|string',
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240'
        ]);

        $path = $request->file('file')->store('documents/loan_applications/' . $application->id, 'public');
        
        $document = new ClientDocument([
            'user_id' => $application->client_id,
            'document_type' => $request->document_type,
            'name' => $request->name,
            'file_path' => $path,
            'upload_date' => now(),
            'status' => 'pending_review'
        ]);

        $application->documents()->save($document);

        return redirect()
            ->back()
            ->with('success', 'Documento cargado exitosamente.');
    }
} 