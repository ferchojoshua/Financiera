<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Ejecuta el seeder.
     */
    public function run()
    {
        // Comenzar transacción
        DB::beginTransaction();

        try {
            // Crear roles del sistema
            $roles = [
                [
                    'name' => 'Super Administrador',
                    'slug' => 'superadmin',
                    'description' => 'Acceso completo a todas las funcionalidades del sistema',
                    'is_system' => true
                ],
                [
                    'name' => 'Administrador',
                    'slug' => 'admin',
                    'description' => 'Administrador del sistema con acceso a la mayoría de funcionalidades',
                    'is_system' => true
                ],
                [
                    'name' => 'Supervisor',
                    'slug' => 'supervisor',
                    'description' => 'Supervisa las operaciones y tiene acceso a reportes',
                    'is_system' => true
                ],
                [
                    'name' => 'Cajero',
                    'slug' => 'caja',
                    'description' => 'Maneja operaciones de caja y pagos',
                    'is_system' => true
                ],
                [
                    'name' => 'Colector',
                    'slug' => 'colector',
                    'description' => 'Responsable de cobranzas en campo',
                    'is_system' => true
                ]
            ];

            foreach ($roles as $roleData) {
                Role::firstOrCreate(
                    ['slug' => $roleData['slug']],
                    $roleData
                );
            }

            // Crear permisos por módulo
            $permissionsByModule = [
                'dashboard' => [
                    ['name' => 'Ver Dashboard', 'slug' => 'view-dashboard', 'description' => 'Permite ver el panel de control'],
                    ['name' => 'Administrar Dashboard', 'slug' => 'manage-dashboard', 'description' => 'Permite administrar widgets del panel de control']
                ],
                'usuarios' => [
                    ['name' => 'Ver Usuarios', 'slug' => 'view-users', 'description' => 'Permite ver la lista de usuarios'],
                    ['name' => 'Crear Usuarios', 'slug' => 'create-users', 'description' => 'Permite crear nuevos usuarios'],
                    ['name' => 'Editar Usuarios', 'slug' => 'edit-users', 'description' => 'Permite editar usuarios existentes'],
                    ['name' => 'Eliminar Usuarios', 'slug' => 'delete-users', 'description' => 'Permite eliminar usuarios']
                ],
                'roles' => [
                    ['name' => 'Ver Roles', 'slug' => 'view-roles', 'description' => 'Permite ver la lista de roles'],
                    ['name' => 'Crear Roles', 'slug' => 'create-roles', 'description' => 'Permite crear nuevos roles'],
                    ['name' => 'Editar Roles', 'slug' => 'edit-roles', 'description' => 'Permite editar roles existentes'],
                    ['name' => 'Eliminar Roles', 'slug' => 'delete-roles', 'description' => 'Permite eliminar roles']
                ],
                'clientes' => [
                    ['name' => 'Ver Clientes', 'slug' => 'view-clients', 'description' => 'Permite ver la lista de clientes'],
                    ['name' => 'Crear Clientes', 'slug' => 'create-clients', 'description' => 'Permite crear nuevos clientes'],
                    ['name' => 'Editar Clientes', 'slug' => 'edit-clients', 'description' => 'Permite editar clientes existentes'],
                    ['name' => 'Eliminar Clientes', 'slug' => 'delete-clients', 'description' => 'Permite eliminar clientes']
                ],
                'préstamos' => [
                    ['name' => 'Ver Préstamos', 'slug' => 'view-loans', 'description' => 'Permite ver la lista de préstamos'],
                    ['name' => 'Crear Préstamos', 'slug' => 'create-loans', 'description' => 'Permite crear nuevos préstamos'],
                    ['name' => 'Aprobar Préstamos', 'slug' => 'approve-loans', 'description' => 'Permite aprobar solicitudes de préstamos'],
                    ['name' => 'Rechazar Préstamos', 'slug' => 'reject-loans', 'description' => 'Permite rechazar solicitudes de préstamos'],
                    ['name' => 'Editar Préstamos', 'slug' => 'edit-loans', 'description' => 'Permite editar préstamos existentes'],
                    ['name' => 'Eliminar Préstamos', 'slug' => 'delete-loans', 'description' => 'Permite eliminar préstamos']
                ],
                'pagos' => [
                    ['name' => 'Ver Pagos', 'slug' => 'view-payments', 'description' => 'Permite ver pagos realizados'],
                    ['name' => 'Registrar Pagos', 'slug' => 'register-payments', 'description' => 'Permite registrar nuevos pagos'],
                    ['name' => 'Anular Pagos', 'slug' => 'cancel-payments', 'description' => 'Permite anular pagos realizados']
                ],
                'cobranza' => [
                    ['name' => 'Ver Cobranzas', 'slug' => 'view-collections', 'description' => 'Permite ver cobranzas pendientes'],
                    ['name' => 'Asignar Cobranzas', 'slug' => 'assign-collections', 'description' => 'Permite asignar cobranzas a colectores'],
                    ['name' => 'Reportar Visitas', 'slug' => 'report-visits', 'description' => 'Permite reportar visitas de cobranza']
                ],
                'reportes' => [
                    ['name' => 'Ver Reportes Básicos', 'slug' => 'view-basic-reports', 'description' => 'Permite ver reportes básicos'],
                    ['name' => 'Ver Reportes Avanzados', 'slug' => 'view-advanced-reports', 'description' => 'Permite ver reportes avanzados'],
                    ['name' => 'Exportar Reportes', 'slug' => 'export-reports', 'description' => 'Permite exportar reportes a diferentes formatos']
                ],
                'configuracion' => [
                    ['name' => 'Ver Configuración', 'slug' => 'view-settings', 'description' => 'Permite ver la configuración del sistema'],
                    ['name' => 'Editar Configuración', 'slug' => 'edit-settings', 'description' => 'Permite modificar la configuración del sistema']
                ]
            ];

            foreach ($permissionsByModule as $module => $permissions) {
                foreach ($permissions as $permissionData) {
                    Permission::firstOrCreate(
                        ['slug' => $permissionData['slug']],
                        array_merge($permissionData, ['module' => $module, 'is_system' => true])
                    );
                }
            }

            // Asignar permisos a roles
            $rolePermissions = [
                'superadmin' => ['*'], // Todos los permisos
                'admin' => [
                    'view-dashboard', 'manage-dashboard',
                    'view-users', 'create-users', 'edit-users',
                    'view-roles', 'create-roles', 'edit-roles',
                    'view-clients', 'create-clients', 'edit-clients', 'delete-clients',
                    'view-loans', 'create-loans', 'approve-loans', 'reject-loans', 'edit-loans',
                    'view-payments', 'register-payments', 'cancel-payments',
                    'view-collections', 'assign-collections',
                    'view-basic-reports', 'view-advanced-reports', 'export-reports',
                    'view-settings', 'edit-settings'
                ],
                'supervisor' => [
                    'view-dashboard',
                    'view-users',
                    'view-clients', 'create-clients', 'edit-clients',
                    'view-loans', 'create-loans', 'approve-loans', 'reject-loans',
                    'view-payments', 'register-payments',
                    'view-collections', 'assign-collections',
                    'view-basic-reports', 'view-advanced-reports', 'export-reports'
                ],
                'caja' => [
                    'view-dashboard',
                    'view-clients',
                    'view-loans',
                    'view-payments', 'register-payments',
                    'view-basic-reports'
                ],
                'colector' => [
                    'view-dashboard',
                    'view-clients',
                    'view-loans',
                    'view-collections', 'report-visits',
                    'view-payments', 'register-payments'
                ]
            ];

            foreach ($rolePermissions as $roleSlug => $permissions) {
                $role = Role::where('slug', $roleSlug)->first();
                if (!$role) continue;

                if ($permissions[0] === '*') {
                    // Asignar todos los permisos
                    $allPermissions = Permission::all();
                    $role->permissions()->sync($allPermissions->pluck('id')->toArray());
                } else {
                    // Asignar permisos específicos
                    $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id')->toArray();
                    $role->permissions()->sync($permissionIds);
                }
            }

            // Configurar permisos de módulos
            $modulePermissions = [
                'superadmin' => [
                    'dashboard' => true,
                    'wallet' => true,
                    'clients_regular' => true,
                    'clients_pyme' => true,
                    'credit_requests' => true,
                    'scoring' => true,
                    'guarantees' => true,
                    'financial_products' => true,
                    'simulator' => true,
                    'payments' => true,
                    'collections' => true,
                    'payment_agreements' => true,
                    'reports' => true,
                    'canceled_reports' => true
                ],
                'admin' => [
                    'dashboard' => true,
                    'wallet' => true,
                    'clients_regular' => true,
                    'clients_pyme' => true,
                    'credit_requests' => true,
                    'scoring' => true,
                    'guarantees' => true,
                    'financial_products' => true,
                    'simulator' => true,
                    'payments' => true,
                    'collections' => true,
                    'payment_agreements' => true,
                    'reports' => true,
                    'canceled_reports' => true
                ],
                'supervisor' => [
                    'dashboard' => true,
                    'wallet' => false,
                    'clients_regular' => true,
                    'clients_pyme' => false,
                    'credit_requests' => true,
                    'scoring' => true,
                    'guarantees' => false,
                    'financial_products' => false,
                    'simulator' => true,
                    'payments' => true,
                    'collections' => false,
                    'payment_agreements' => false,
                    'reports' => true,
                    'canceled_reports' => false
                ],
                'caja' => [
                    'dashboard' => true,
                    'wallet' => false,
                    'clients_regular' => false,
                    'clients_pyme' => false,
                    'credit_requests' => false,
                    'scoring' => false,
                    'guarantees' => false,
                    'financial_products' => false,
                    'simulator' => false,
                    'payments' => true,
                    'collections' => false,
                    'payment_agreements' => false,
                    'reports' => false,
                    'canceled_reports' => false
                ],
                'colector' => [
                    'dashboard' => true,
                    'wallet' => false,
                    'clients_regular' => false,
                    'clients_pyme' => false,
                    'credit_requests' => false,
                    'scoring' => false,
                    'guarantees' => false,
                    'financial_products' => false,
                    'simulator' => false,
                    'payments' => true,
                    'collections' => true,
                    'payment_agreements' => false,
                    'reports' => false,
                    'canceled_reports' => false
                ]
            ];

            foreach ($modulePermissions as $roleSlug => $modules) {
                $role = Role::where('slug', $roleSlug)->first();
                if (!$role) continue;

                foreach ($modules as $module => $hasAccess) {
                    DB::table('role_module_permissions')->updateOrInsert(
                        ['role_id' => $role->id, 'module' => $module],
                        [
                            'has_access' => $hasAccess,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }
            }

            // Crear usuario superadmin si no existe
            if (!User::where('email', 'admin@sistema.com')->exists()) {
                User::create([
                    'name' => 'Administrador',
                    'last_name' => 'Sistema',
                    'email' => 'admin@sistema.com',
                    'password' => Hash::make('admin123'),
                    'role' => 'superadmin',
                    'status' => 'active'
                ]);
            }

            DB::commit();
            $this->command->info('Roles y permisos creados con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error al crear roles y permisos: ' . $e->getMessage());
        }
    }
}
