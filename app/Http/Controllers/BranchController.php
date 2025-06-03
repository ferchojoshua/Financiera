<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    /**
     * Constructor del controlador
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::with('manager')
            ->orderBy('name')
            ->get();
            
        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener todos los usuarios que pueden ser gerentes
        $managers = User::whereIn('role', ['admin', 'supervisor'])
            ->orderBy('name')
            ->get();
            
        return view('branches.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:branches,code',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Crear la sucursal
            $branch = new Branch();
            $branch->name = $request->name;
            $branch->code = $request->code;
            $branch->address = $request->address;
            $branch->city = $request->city;
            $branch->state = $request->state;
            $branch->country = $request->country;
            $branch->phone = $request->phone;
            $branch->email = $request->email;
            $branch->manager_id = $request->manager_id;
            $branch->status = $request->status;
            $branch->description = $request->description;
            $branch->created_by = Auth::id();
            $branch->save();
            
            DB::commit();
            
            return redirect()->route('branches.index')
                ->with('success', 'Sucursal creada correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear la sucursal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        // Cargar las relaciones
        $branch->load('manager', 'users', 'wallets', 'credits');
        
        // Estadísticas de la sucursal
        $stats = [
            'users_count' => $branch->users()->count(),
            'wallets_count' => $branch->wallets()->count(),
            'active_credits' => $branch->credits()->where('status', 'inprogress')->count(),
            'closed_credits' => $branch->credits()->where('status', 'close')->count(),
            'total_amount' => $branch->credits()->where('status', 'inprogress')->sum('amount_neto'),
        ];
        
        return view('branches.show', compact('branch', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        // Obtener todos los usuarios que pueden ser gerentes
        $managers = User::whereIn('role', ['admin', 'supervisor'])
            ->orderBy('name')
            ->get();
            
        return view('branches.edit', compact('branch', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        // Validar los datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:branches,code,' . $branch->id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Actualizar la sucursal
            $branch->name = $request->name;
            $branch->code = $request->code;
            $branch->address = $request->address;
            $branch->city = $request->city;
            $branch->state = $request->state;
            $branch->country = $request->country;
            $branch->phone = $request->phone;
            $branch->email = $request->email;
            $branch->manager_id = $request->manager_id;
            $branch->status = $request->status;
            $branch->description = $request->description;
            $branch->updated_by = Auth::id();
            $branch->save();
            
            DB::commit();
            
            return redirect()->route('branches.index')
                ->with('success', 'Sucursal actualizada correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la sucursal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        try {
            // Verificar si hay elementos relacionados
            if ($branch->users()->count() > 0 || $branch->wallets()->count() > 0 || $branch->credits()->count() > 0) {
                return redirect()->route('branches.index')
                    ->with('error', 'No se puede eliminar la sucursal porque tiene elementos asociados');
            }
            
            DB::beginTransaction();
            
            $branch->delete();
            
            DB::commit();
            
            return redirect()->route('branches.index')
                ->with('success', 'Sucursal eliminada correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('branches.index')
                ->with('error', 'Error al eliminar la sucursal: ' . $e->getMessage());
        }
    }
}
