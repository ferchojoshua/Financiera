<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RoleModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Módulos del sistema
        $modules = [
            'dashboard',
            'clientes',
            'creditos',
            'pagos',
            'cobranzas',
            'reportes',
            'configuracion',
            'usuarios',
            'contabilidad',
            'sucursales',
            'billeteras'
        ];
        
        // Obtener todos los roles
        $roles = Role::all();
        
        foreach ($roles as $role) {
            foreach ($modules as $module) {
                // Verificar si ya existe el permiso
                $exists = DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module', $module)
                    ->exists();
                
                if (!$exists) {
                    // Determinar si tiene acceso según el rol
                    $hasAccess = ($role->slug === 'superadmin' || $role->slug === 'admin') ? true : false;
                    
                    // Casos especiales
                    if ($role->slug === 'supervisor') {
                        $hasAccess = in_array($module, ['dashboard', 'clientes', 'creditos', 'cobranzas']) ? true : false;
                    } elseif ($role->slug === 'agent' || $role->slug === 'colector') {
                        $hasAccess = in_array($module, ['dashboard', 'clientes', 'pagos', 'cobranzas']) ? true : false;
                    } elseif ($role->slug === 'caja') {
                        $hasAccess = in_array($module, ['dashboard', 'pagos', 'creditos']) ? true : false;
                    }
                    
                    // Insertar el permiso
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module' => $module,
                        'has_access' => $hasAccess ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
        
        $this->command->info('Permisos de módulos para roles insertados correctamente.');
    }
}
