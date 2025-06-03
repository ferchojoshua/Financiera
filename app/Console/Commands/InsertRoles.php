<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insertar los roles básicos y sus permisos en el sistema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Insertando roles básicos y permisos...');
        
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
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                $role
            );
        }
        
        $this->info('Roles insertados correctamente.');
        
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
                
                DB::table('role_module_permissions')->updateOrInsert(
                    ['role_id' => $roleId, 'module' => $module],
                    [
                        'has_access' => $hasAccess,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
        
        $this->info('Permisos de módulos asignados correctamente a los roles.');
        
        return Command::SUCCESS;
    }
}
