<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:access-admin-area');
    }

    /**
     * Muestra la lista de roles
     */
    public function index()
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta área');
        }

        // Obtener roles según permisos
        if (auth()->user()->isSuperAdmin()) {
            $roles = Role::orderBy('name')->paginate(10);
        } else {
            // Admin normal no puede ver roles de sistema
            $roles = Role::where('is_system', false)->orderBy('name')->paginate(10);
        }

        return view('config.roles.index', compact('roles'));
    }

    /**
     * Muestra el formulario para crear un nuevo rol
     */
    public function create()
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear roles');
        }

        // Obtener todos los permisos agrupados por módulo
        $permissionsByModule = Permission::all()->groupBy('module');

        return view('config.roles.create', compact('permissionsByModule'));
    }

    /**
     * Almacena un nuevo rol en la base de datos
     */
    public function store(Request $request)
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para crear roles');
        }

        // Validar datos
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            DB::beginTransaction();

            // Crear el rol
            $role = new Role();
            $role->name = $request->name;
            $role->slug = $request->slug;
            $role->description = $request->description;
            $role->is_system = false;
            $role->created_by = auth()->id();
            $role->save();

            // Asignar permisos si existen
            if ($request->has('permissions')) {
                $role->permissions()->attach($request->permissions);
            }

            DB::commit();
            return redirect()->route('config.roles.index')
                ->with('success', 'Rol creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear el rol: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un rol
     */
    public function edit(Role $role)
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para editar roles');
        }

        // Verificar si es un rol de sistema y el usuario no es superadmin
        if ($role->is_system && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('config.roles.index')
                ->with('error', 'No puedes editar un rol del sistema');
        }

        // Obtener todos los permisos agrupados por módulo
        $permissionsByModule = Permission::all()->groupBy('module');
        
        // Obtener los IDs de los permisos asignados al rol
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('config.roles.edit', compact('role', 'permissionsByModule', 'rolePermissions'));
    }

    /**
     * Actualiza un rol en la base de datos
     */
    public function update(Request $request, Role $role)
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para actualizar roles');
        }

        // Verificar si es un rol de sistema y el usuario no es superadmin
        if ($role->is_system && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('config.roles.index')
                ->with('error', 'No puedes editar un rol del sistema');
        }

        // Validar datos
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role)],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', Rule::unique('roles')->ignore($role)],
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar el rol
            $role->name = $request->name;
            
            // Si no es un rol de sistema o si el usuario es superadmin, permitir cambiar el slug
            if (!$role->is_system || auth()->user()->isSuperAdmin()) {
                $role->slug = $request->slug;
            }
            
            $role->description = $request->description;
            $role->updated_by = auth()->id();
            $role->save();

            // Sincronizar permisos
            // Si es un rol de sistema y el usuario no es superadmin, mantener los permisos del sistema
            if ($role->is_system && !auth()->user()->isSuperAdmin()) {
                // Obtener permisos actuales
                $currentPermissions = $role->permissions->pluck('id')->toArray();
                
                // Obtener permisos de sistema que no pueden ser modificados
                $systemPermissions = $role->permissions()
                    ->where('is_system', true)
                    ->pluck('permissions.id')
                    ->toArray();
                
                // Obtener nuevos permisos enviados o array vacío si no hay
                $newPermissions = $request->permissions ?? [];
                
                // Combinar permisos de sistema con los nuevos
                $finalPermissions = array_unique(array_merge($systemPermissions, $newPermissions));
                
                // Sincronizar
                $role->permissions()->sync($finalPermissions);
            } else {
                // Si no es rol de sistema o el usuario es superadmin, sincronizar normalmente
                $role->permissions()->sync($request->permissions ?? []);
            }

            DB::commit();
            return redirect()->route('config.roles.index')
                ->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar el rol: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Elimina un rol de la base de datos
     */
    public function destroy(Role $role)
    {
        // Verificar que el usuario tenga permisos
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'No tienes permisos para eliminar roles');
        }

        // No permitir eliminar roles del sistema
        if ($role->is_system) {
            return redirect()->route('config.roles.index')
                ->with('error', 'No puedes eliminar un rol del sistema');
        }

        // Verificar si hay usuarios usando este rol
        $usersCount = \App\Models\User::where('role', $role->slug)->count();
        if ($usersCount > 0) {
            return redirect()->route('config.roles.index')
                ->with('error', "No puedes eliminar este rol porque hay {$usersCount} usuarios asociados a él");
        }

        try {
            DB::beginTransaction();
            
            // Eliminar relaciones con permisos
            $role->permissions()->detach();
            
            // Eliminar rol
            $role->delete();
            
            DB::commit();
            return redirect()->route('config.roles.index')
                ->with('success', 'Rol eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('config.roles.index')
                ->with('error', 'Error al eliminar el rol: ' . $e->getMessage());
        }
    }
} 