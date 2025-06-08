<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateModulePermissions extends Command
{
    protected $signature = 'permissions:update';
    protected $description = 'Actualiza los permisos de módulos para todos los roles';

    public function handle()
    {
        $allModules = [
            'dashboard',
            'clientes',
            'creditos',
            'pagos',
            'cobranzas',
            'reportes',
            'configuracion',
            'usuarios',
            'contabilidad',
            'routes',
            'wallet',
            'pymes',
            'garantias',
            'caja',
            'auditoria',
            'solicitudes',
            'scoring',
            'branches',
            'admin',
            'simulador',
            'gastos',
            'productos',
            'sucursales',
            'billeteras',
            'empresa',
            'permisos',
            'preferencias'
        ];

        // Obtener todos los roles
        $roles = DB::table('roles')->get();
        $count = 0;

        foreach ($roles as $role) {
            $isAdmin = in_array($role->slug, ['admin', 'superadmin']);

            foreach ($allModules as $module) {
                // Verificar si ya existe el permiso
                $existingPermission = DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module', $module)
                    ->first();

                if ($existingPermission) {
                    // Si es admin/superadmin, asegurar que tenga acceso
                    if ($isAdmin && !$existingPermission->has_access) {
                        DB::table('role_module_permissions')
                            ->where('id', $existingPermission->id)
                            ->update([
                                'has_access' => true,
                                'updated_at' => now()
                            ]);
                        $count++;
                    }
                } else {
                    // Crear nuevo permiso
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module' => $module,
                        'has_access' => $isAdmin, // true para admin/superadmin, false para otros roles
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $count++;
                }
            }
        }

        $this->info("Se actualizaron/crearon {$count} permisos de módulos.");
    }
}