<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\User;
use App\Models\SystemPreference;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    /**
     * Constructor - Sólo admin y superadmin pueden acceder
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:access-admin-area');
    }

    /**
     * Mostrar vista de configuración general
     */
    public function index()
    {
        // Verificar que el usuario tenga permisos de administrador
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección');
        }
        
        return view('config.index');
    }

    /**
     * Mostrar el formulario para editar la información de la empresa
     */
    public function editCompany()
    {
        $company = CompanyInfo::first();
        return view('config.company.edit', compact('company'));
    }

    /**
     * Actualizar la información de la empresa
     */
    public function updateCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ruc' => 'required|string|max:20',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'legal_representative' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'receipt_message' => 'nullable|string',
            'contract_footer' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'footer_text' => 'nullable|string',
        ]);

        $company = CompanyInfo::first();
        if (!$company) {
            $company = new CompanyInfo();
        }

        $company->name = $request->name;
        $company->ruc = $request->ruc;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->email = $request->email;
        $company->website = $request->website;
        $company->legal_representative = $request->legal_representative;
        $company->receipt_message = $request->receipt_message;
        $company->contract_footer = $request->contract_footer;
        $company->payment_terms = $request->payment_terms;
        $company->footer_text = $request->footer_text;

        // Procesar el logo si se ha subido
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $company->logo_path = $path;
        }

        $company->save();

        return redirect()->route('config.company.edit')->with('success', 'Información de la empresa actualizada correctamente');
    }

    /**
     * Mostrar listado de usuarios
     */
    public function usersIndex()
    {
        // Obtener usuarios
        $users = \App\Models\User::with('roles')->get();
        
        // Obtener roles para el filtro
        $roles = \App\Models\Role::where('is_active', true)->get();
        
        return view('config.users.index', compact('users', 'roles'));
    }

    /**
     * Mostrar formulario para crear un nuevo usuario
     */
    public function usersCreate()
    {
        // Verificar permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear usuarios');
        }

        // Obtener todos los roles activos
        $roles = \App\Models\Role::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Si no es superadmin, filtrar el rol de superadmin
        if (!auth()->user()->isSuperAdmin()) {
            $roles = $roles->filter(function($role) {
                return $role->slug !== 'superadmin';
            })->values();
        }

        // Asegurar que el menú lateral esté visible
        $showSidebar = true;
        
        return view('config.users.create', compact('roles', 'showSidebar'));
    }

    /**
     * Almacenar un nuevo usuario
     */
    public function usersStore(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:50|unique:users',
            'nit' => 'nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,slug',
            'level' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:50',
        ]);
        
        // Crear el usuario
        $user = new User();
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->nit = $request->nit ?? '';
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user->level = $request->level ?? '1'; // Asignar valor predeterminado si es nulo
        $user->phone = $request->phone ?? '';
        $user->address = $request->address ?? '';
        $user->country = $request->country ?? '';
        $user->active_user = 1;
        $user->save();
        
        // Redireccionar con mensaje de éxito
        return redirect()->route('config.users.index')->with('success', 'Usuario creado correctamente');
    }

    /**
     * Mostrar formulario para editar un usuario
     */
    public function usersEdit($id)
    {
        $user = User::findOrFail($id);
        
        // Verificar permisos - solo superadmin puede editar a otro superadmin
        if ($user->role === 'superadmin' && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('config.users.index')->with('error', 'No tienes permisos para editar este usuario');
        }

        return view('config.users.edit', compact('user'));
    }

    /**
     * Actualizar un usuario existente
     */
    public function usersUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Verificar permisos - solo superadmin puede editar a otro superadmin
        if ($user->role === 'superadmin' && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('config.users.index')->with('error', 'No tienes permisos para editar este usuario');
        }

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
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => [
                'required', 
                Rule::in(['superadmin', 'admin', 'supervisor', 'caja', 'colector', 'user']),
                // Solo superadmins pueden asignar rol de superadmin
                function ($attribute, $value, $fail) use ($user) {
                    if ($value === 'superadmin' && !auth()->user()->isSuperAdmin()) {
                        $fail('No tienes permisos para asignar este rol.');
                    }
                },
            ],
            'status' => 'required|in:active,inactive',
            'nit' => 'nullable|string|max:20',
            'zone' => 'nullable|string|max:100',
            'business_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:20',
            'business_sector' => 'nullable|string|max:100',
            'economic_activity' => 'nullable|string|max:255',
        ]);

        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->role = $request->role;
        $user->level = $request->level; // Para mantener compatibilidad con el sistema actual
        $user->status = $request->status;
        
        // Campos específicos según el rol
        if ($request->role === 'colector') {
            $user->nit = $request->nit;
            $user->zone = $request->zone;
        } elseif ($request->role === 'user') {
            $user->business_name = $request->business_name;
            $user->tax_id = $request->tax_id;
            $user->business_sector = $request->business_sector;
            $user->economic_activity = $request->economic_activity;
        }
        
        $user->save();

        return redirect()->route('config.users.index')->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar un usuario
     */
    public function usersDestroy($id)
    {
        $user = User::findOrFail($id);
        
        // Verificar permisos - solo superadmin puede eliminar a otro superadmin
        if ($user->role === 'superadmin' && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('config.users.index')->with('error', 'No tienes permisos para eliminar este usuario');
        }
        
        // No permitir eliminar el propio usuario
        if ($user->id === auth()->id()) {
            return redirect()->route('config.users.index')->with('error', 'No puedes eliminar tu propio usuario');
        }

        $user->delete();

        return redirect()->route('config.users.index')->with('success', 'Usuario eliminado correctamente');
    }

    /**
     * Mostrar vista de gestión de permisos
     */
    public function permisosIndex()
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta área');
        }
        
        // Obtener roles disponibles (excepto superadmin)
        $roles = Role::where('slug', '!=', 'superadmin')
                     ->where('is_active', true)
                     ->pluck('name', 'slug')
                     ->toArray();
        
        // Módulos del sistema - Organizados por categorías
        $modulos = [
            // Módulos Principales
            'dashboard' => 'Dashboard',
            'billetera' => 'Billetera',
            
            // Gestión de Clientes
            'clientes' => 'Clientes Regulares',
            'pymes' => 'Clientes PYME',
            
            // Créditos y Préstamos
            'solicitudes' => 'Solicitudes de Crédito',
            'analisis' => 'Análisis y Scoring',
            'garantias' => 'Garantías',
            'productos' => 'Productos Financieros',
            'simulador' => 'Simulador',
            
            // Pagos y Cobranza
            'pagos' => 'Pagos',
            'cobranza' => 'Cobranza',
            'cobranzas' => 'Gestión de Cobranzas',
            'acuerdos' => 'Acuerdos de Pago',
            
            // Reportes y Contabilidad
            'reportes' => 'Reportes',
            'reportes_cancelados' => 'Reportes Cancelados',
            'reportes_desembolsos' => 'Reportes Desembolsos',
            'reportes_activos' => 'Reportes Activos',
            'reportes_vencidos' => 'Reportes Vencidos',
            'reportes_por_cancelar' => 'Reportes Por Cancelar',
            'cierre_mes' => 'Cierre de Mes',
            'recuperacion_desembolsos' => 'Recuperación y Desembolsos',
            
            // Rutas y Cobranza
            'rutas' => 'Gestión de Rutas',
            'asignacion_creditos' => 'Asignación de Créditos',
            
            // Sistema y Auditoría
            'auditoria' => 'Registro de Auditoría',
            'seguridad' => 'Seguridad',
            
            // Configuración del Sistema
            'configuracion' => 'Configuración General',
            'usuarios' => 'Gestión de Usuarios',
            'permisos' => 'Permisos de Acceso',
            'preferencias' => 'Preferencias del Sistema',
            'empresa' => 'Información de la Empresa',
            'caja' => 'Caja y Contabilidad',
            'contabilidad' => 'Contabilidad'
        ];
        
        // Cargar permisos actuales desde la BD
        $permisosDB = DB::table('role_module_permissions')
            ->join('roles', 'roles.id', '=', 'role_module_permissions.role_id')
            ->where('roles.slug', '!=', 'superadmin')
            ->where('role_module_permissions.has_access', true)
            ->select('roles.slug as role_slug', 'role_module_permissions.module')
            ->get();
            
        // Convertir a formato adecuado para la vista
        $permisos = [];
        foreach ($permisosDB as $permiso) {
            if (!isset($permisos[$permiso->role_slug])) {
                $permisos[$permiso->role_slug] = [];
            }
            $permisos[$permiso->role_slug][] = $permiso->module;
        }
        
        return view('config.permisos.index', compact('roles', 'modulos', 'permisos'));
    }

    /**
     * Mostrar vista de preferencias del sistema
     */
    public function systemPreferences()
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta área');
        }

        // Obtener todos los roles excepto superadmin
        $roles = Role::where('slug', '!=', 'superadmin')
                     ->where('is_active', true)
                     ->orderBy('name')
                     ->get();

        // Obtener configuración de permisos
        $permissions = DB::table('role_module_permissions')->get()->groupBy('role_id');
        
        // Convertir a array para facilitar acceso en la vista
        $permissionsArray = [];
        foreach ($permissions as $roleId => $perms) {
            foreach ($perms as $perm) {
                $permissionsArray[$roleId][$perm->module] = $perm->has_access;
            }
        }

        return view('config.system_preferences', [
            'roles' => $roles,
            'permissions' => $permissionsArray
        ]);
    }

    /**
     * Mostrar formulario para editar preferencias del sistema
     */
    public function preferencesEdit()
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta área');
        }

        // Obtener preferencias actuales o valores por defecto
        $preferences = SystemPreference::first();
        if (!$preferences) {
            $preferences = new SystemPreference();
        }

        return view('config.preferences.edit', compact('preferences'));
    }

    /**
     * Actualizar preferencias del sistema
     */
    public function preferencesUpdate(Request $request)
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para modificar la configuración del sistema');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'theme_color' => 'nullable|string|max:20',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'default_language' => 'nullable|string|max:10',
            'default_currency' => 'nullable|string|max:10',
            'enable_customer_portal' => 'nullable|boolean',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        $preferences = SystemPreference::first();
        if (!$preferences) {
            $preferences = new SystemPreference();
        }

        // Actualizar campos
        $preferences->company_name = $request->company_name;
        $preferences->theme_color = $request->theme_color;
        $preferences->email_notifications = $request->has('email_notifications');
        $preferences->sms_notifications = $request->has('sms_notifications');
        $preferences->default_language = $request->default_language;
        $preferences->default_currency = $request->default_currency;
        $preferences->enable_customer_portal = $request->has('enable_customer_portal');
        $preferences->maintenance_mode = $request->has('maintenance_mode');

        // Procesar el logo si se ha subido
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($preferences->logo_path && Storage::disk('public')->exists($preferences->logo_path)) {
                Storage::disk('public')->delete($preferences->logo_path);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $preferences->logo_path = $path;
        }

        $preferences->save();

        return redirect()->route('config.preferences.edit')
            ->with('success', 'Preferencias del sistema actualizadas correctamente');
    }

    /**
     * Muestra la lista de usuarios del sistema
     */
    public function usersList()
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta área');
        }

        // Obtener usuarios según permisos
        if (auth()->user()->isSuperAdmin()) {
            $users = User::orderBy('name')->paginate(10);
        } else {
            // Admin normal no puede ver superadmins
            // Filtramos por level (evitando el campo role)
            $users = User::where(function($query) {
                $query->where('level', '!=', 'admin')
                      ->orWhereNull('level');
            })->orderBy('name')->paginate(10);
        }

        return view('config.users.index', compact('users'));
    }

    /**
     * Actualiza los permisos de acceso para cada rol
     */
    public function permisosUpdate(Request $request)
    {
        // Verificar permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para modificar la configuración del sistema');
        }
        
        try {
            $permisos = $request->input('permisos', []);
            
            // Obtener roles (excepto superadmin que siempre tiene todos los permisos)
            $roles = Role::where('slug', '!=', 'superadmin')->get();
            
            // Iniciar transacción
            DB::beginTransaction();
            
            // Obtener todos los módulos del sistema
            $allModules = [
                'configuracion', 'clientes', 'pymes', 'reportes', 'cobranzas', 
                'seguridad', 'rutas', 'caja', 'usuarios', 'dashboard', 
                'wallet', 'billetera', 'garantias', 'simulador', 'pagos', 
                'cobranza', 'contabilidad', 'auditoria'
            ];
            
            // Procesar permisos de cada rol
            foreach ($roles as $role) {
                // Admin siempre tiene todos los accesos (pero procesamos por si cambia en futuro)
                $forceFullAccess = ($role->slug === 'admin');
                
                foreach ($allModules as $module) {
                    // Verificar si el módulo está marcado para este rol o si es admin
                    $hasAccess = $forceFullAccess || 
                                (isset($permisos[$role->slug]) && in_array($module, $permisos[$role->slug]));
                    
                    // Actualizar o insertar el permiso
                    DB::table('role_module_permissions')
                        ->updateOrInsert(
                            ['role_id' => $role->id, 'module' => $module],
                            [
                                'has_access' => $hasAccess,
                                'updated_at' => now(),
                                'created_at' => now()
                            ]
                        );
                }
            }
            
            // Confirmar transacción
            DB::commit();
            
            return redirect()->route('config.permisos.index')
                ->with('success', 'Permisos actualizados correctamente');
                
        } catch (\Exception $e) {
            // Revertir en caso de error
            DB::rollBack();
            
            // Registrar el error
            \Log::error('Error al actualizar permisos: ' . $e->getMessage());
            
            return redirect()->route('config.permisos.index')
                ->with('error', 'Error al actualizar permisos: ' . $e->getMessage());
        }
    }
} 