<?php

namespace App\Http\Controllers;

use App\db_countries;
use App\db_supervisor_has_agent;
use App\db_wallet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Constructor - Verificar permisos
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar listado de usuarios
     */
    public function index(Request $request)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && Auth::user()->role != 'superadmin') {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección');
        }

        // Consulta base de usuarios
        $query = User::query();

        // Aplicar filtros si existen
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Si no es superadmin, no mostrar superadmins
        if (Auth::user()->role != 'superadmin') {
            $query->where('role', '!=', 'superadmin');
        }

        // Paginar resultados
        $users = $query->orderBy('id', 'desc')->paginate(15);

        // Obtener roles para el filtro
        $roles = [
            'superadmin' => 'Super Administrador',
            'admin' => 'Administrador',
            'supervisor' => 'Supervisor',
            'caja' => 'Cajero',
            'colector' => 'Colector',
            'user' => 'Cliente'
        ];

        return view('admin.user.index', compact('users', 'roles'));
    }

    /**
     * Mostrar formulario para crear nuevo usuario
     */
    public function create()
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && Auth::user()->role != 'superadmin') {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear usuarios');
        }

        $roles = [
            'admin' => 'Administrador',
            'supervisor' => 'Supervisor',
            'caja' => 'Cajero',
            'colector' => 'Colector',
            'user' => 'Cliente'
        ];

        // Solo superadmin puede crear superadmins
        if (Auth::user()->role == 'superadmin') {
            $roles = ['superadmin' => 'Super Administrador'] + $roles;
        }

        return view('admin.user.create', compact('roles'));
    }

    /**
     * Almacenar nuevo usuario
     */
    public function store(Request $request)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && Auth::user()->role != 'superadmin') {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear usuarios');
        }

        // Validar datos
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => [
                'required',
                Rule::in(['superadmin', 'admin', 'supervisor', 'caja', 'colector', 'user']),
                // Sólo superadmin puede crear superadmins
                function ($attribute, $value, $fail) {
                    if ($value === 'superadmin' && Auth::user()->role !== 'superadmin') {
                        $fail('No tienes permisos para asignar este rol.');
                    }
                },
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Crear usuario
        $user = new User();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->level = $request->role; // Para mantener compatibilidad
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->active_user = 1;
        $user->status = 'active';
        $user->save();

        return redirect()->route('admin.user.index')
            ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Mostrar datos de un usuario
     */
    public function show($id)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && Auth::user()->role != 'superadmin') {
            return redirect()->route('home')->with('error', 'No tienes permisos para ver detalles de usuarios');
        }

        $user = User::findOrFail($id);

        // Solo superadmin puede ver superadmins
        if ($user->role == 'superadmin' && Auth::user()->role != 'superadmin') {
            return redirect()->route('admin.user.index')
                ->with('error', 'No tienes permisos para ver este usuario');
        }

        return view('admin.user.show', compact('user'));
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function edit($id)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && Auth::user()->role != 'superadmin') {
            return redirect()->route('home')->with('error', 'No tienes permisos para editar usuarios');
        }

        $user = User::findOrFail($id);

        // Solo superadmin puede editar superadmins
        if ($user->role == 'superadmin' && Auth::user()->role != 'superadmin') {
            return redirect()->route('admin.user.index')
                ->with('error', 'No tienes permisos para editar este usuario');
        }

        $roles = [
            'admin' => 'Administrador',
            'supervisor' => 'Supervisor',
            'caja' => 'Cajero',
            'colector' => 'Colector',
            'user' => 'Cliente'
        ];

        // Solo superadmin puede asignar rol de superadmin
        if (Auth::user()->role == 'superadmin') {
            $roles = ['superadmin' => 'Super Administrador'] + $roles;
        }

        return view('admin.user.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar datos de usuario
     */
    public function update(Request $request, $id)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && Auth::user()->role != 'superadmin') {
            return redirect()->route('home')->with('error', 'No tienes permisos para editar usuarios');
        }

        $user = User::findOrFail($id);

        // Solo superadmin puede editar superadmins
        if ($user->role == 'superadmin' && Auth::user()->role != 'superadmin') {
            return redirect()->route('admin.user.index')
                ->with('error', 'No tienes permisos para editar este usuario');
        }

        // Validar datos
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => [
                'required',
                Rule::in(['superadmin', 'admin', 'supervisor', 'caja', 'colector', 'user']),
                // Solo superadmin puede asignar rol de superadmin
                function ($attribute, $value, $fail) use ($user) {
                    if ($value === 'superadmin' && Auth::user()->role !== 'superadmin') {
                        $fail('No tienes permisos para asignar este rol.');
                    }
                    // No permitir cambiar el rol de superadmin si no eres superadmin
                    if ($user->role === 'superadmin' && $value !== 'superadmin' && Auth::user()->role !== 'superadmin') {
                        $fail('No tienes permisos para cambiar el rol de este usuario.');
                    }
                },
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Actualizar usuario
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->role = $request->role;
        $user->level = $request->role; // Para mantener compatibilidad
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->status = $request->status;
        $user->save();

        return redirect()->route('admin.user.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && Auth::user()->role != 'superadmin') {
            return redirect()->route('home')->with('error', 'No tienes permisos para eliminar usuarios');
        }

        $user = User::findOrFail($id);

        // Solo superadmin puede eliminar superadmins
        if ($user->role == 'superadmin' && Auth::user()->role != 'superadmin') {
            return redirect()->route('admin.user.index')
                ->with('error', 'No tienes permisos para eliminar este usuario');
        }

        // No permitir eliminar propio usuario
        if ($user->id == Auth::id()) {
            return redirect()->route('admin.user.index')
                ->with('error', 'No puedes eliminar tu propio usuario');
        }

        $user->delete();

        return redirect()->route('admin.user.index')
            ->with('success', 'Usuario eliminado correctamente');
    }
}
