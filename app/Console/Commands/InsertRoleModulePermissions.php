<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class InsertRoleModulePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insertar permisos de módulos para todos los roles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Insertando permisos de módulos para roles...');
        
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
        
        if ($roles->isEmpty()) {
            $this->error('No hay roles en la base de datos. Primero debe crear los roles.');
            return 1;
        }
        
        $count = 0;
        
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
                    
                    $count++;
                }
            }
        }
        
        $this->info("Se insertaron {$count} permisos de módulos para roles.");
        
        return 0;
    }
} 