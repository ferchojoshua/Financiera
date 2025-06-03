<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar roles básicos
        $roles = [
            [
                'name' => 'Super Administrador',
                'slug' => 'superadmin',
                'description' => 'Acceso completo a todas las funciones del sistema',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
             [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Administrador del sistema con acceso a configuración',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Supervisor',
                'slug' => 'supervisor',
                'description' => 'Supervisor de agentes y operaciones',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Agente',
                'slug' => 'agent',
                'description' => 'Agente de campo para gestión de créditos',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Cliente',
                'slug' => 'client',
                'description' => 'Cliente del sistema',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        // Insertar los roles
        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore($role);
        }
        
        // Definir los módulos del sistema
        $modules = [
            'dashboard', 'clientes', 'creditos', 'pagos', 'cobranzas', 
            'reportes', 'configuracion', 'usuarios', 'contabilidad'
        ];
        
        // Obtener los IDs de los roles
        $roleIds = DB::table('roles')->pluck('id', 'slug');
        
        // Definir permisos por rol
        $rolePermissions = [
            'superadmin' => $modules, // Acceso a todos los módulos
            'admin' => $modules, // Acceso a todos los módulos
            'supervisor' => array_diff($modules, ['configuracion']), // Todos excepto configuración
            'agent' => ['dashboard', 'clientes', 'creditos', 'pagos'],
            'client' => ['dashboard', 'creditos']
        ];
        
        // Asignar permisos de módulos a roles
        foreach ($roleIds as $slug => $roleId) {
            foreach ($modules as $module) {
                $hasAccess = in_array($module, $rolePermissions[$slug] ?? []);
                
                DB::table('role_module_permissions')->insertOrIgnore([
                    'role_id' => $roleId,
                    'module' => $module,
                    'has_access' => $hasAccess,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
