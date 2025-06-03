<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:access {--force : Forzar la actualización de todos los permisos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige los permisos de acceso a módulos para todos los roles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando corrección de permisos de acceso...');
        
        // Definir todos los módulos del sistema
        $allModules = [
            'dashboard' => 'Panel de control',
            'clientes' => 'Gestión de clientes',
            'creditos' => 'Gestión de créditos',
            'pagos' => 'Gestión de pagos',
            'wallet' => 'Billetera',
            'routes' => 'Rutas',
            'cobranzas' => 'Cobranza',
            'reports' => 'Reportes',
            'configuracion' => 'Configuración',
            'usuarios' => 'Usuarios',
            'contabilidad' => 'Contabilidad',
            'gastos' => 'Gastos',
            'pymes' => 'PyMEs',
            'garantias' => 'Garantías',
            'caja' => 'Caja',
            'auditoria' => 'Auditoría',
            'solicitudes' => 'Solicitudes de crédito',
            'scoring' => 'Scoring crediticio',
            'branches' => 'Sucursales',
            'reportes' => 'Informes',
            'productos' => 'Productos financieros',
            'admin' => 'Administración'
        ];
        
        // Roles con acceso completo
        $adminRoles = ['superadmin', 'admin'];
        
        // Permisos por defecto para cada rol
        $defaultPermissions = [
            'agent' => ['dashboard', 'clientes', 'pagos', 'routes', 'wallet'],
            'supervisor' => ['dashboard', 'clientes', 'creditos', 'pagos', 'routes', 'cobranzas', 'reports', 'reportes', 'wallet'],
            'colector' => ['dashboard', 'routes', 'cobranzas', 'pagos'],
            'caja' => ['dashboard', 'pagos', 'caja', 'wallet'],
            'contador' => ['dashboard', 'reports', 'reportes', 'contabilidad', 'gastos'],
            'cobranza' => ['dashboard', 'clientes', 'cobranzas', 'routes'],
            'auditor' => ['dashboard', 'reports', 'reportes', 'auditoria', 'contabilidad'],
            'user' => ['dashboard']
        ];
        
        // Obtener todos los roles
        $roles = DB::table('roles')->get();
        $this->info("Encontrados " . $roles->count() . " roles en el sistema.");
        
        $permissionsFixed = 0;
        $permissionsCreated = 0;
        
        // Procesar cada rol
        foreach ($roles as $role) {
            $this->info("Procesando rol: " . $role->name);
            
            $hasFullAccess = in_array($role->slug, $adminRoles);
            
            // Determinar qué módulos debe tener acceso este rol
            $rolePermissions = $hasFullAccess ? array_keys($allModules) : 
                              (isset($defaultPermissions[$role->slug]) ? $defaultPermissions[$role->slug] : ['dashboard']);
            
            // Procesar cada módulo
            foreach ($allModules as $moduleKey => $moduleName) {
                // Verificar si ya existe un permiso para este rol y módulo
                $existingPermission = DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module', $moduleKey)
                    ->first();
                
                $shouldHaveAccess = $hasFullAccess || in_array($moduleKey, $rolePermissions);
                
                if (!$existingPermission) {
                    // Crear nuevo permiso
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module' => $moduleKey,
                        'has_access' => $shouldHaveAccess,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $permissionsCreated++;
                    
                    $this->line(" - Creado permiso para $moduleKey: " . ($shouldHaveAccess ? 'Acceso' : 'Sin acceso'));
                } else {
                    // Actualizar si es necesario o si se fuerza con --force
                    if ($this->option('force') || ($hasFullAccess && !$existingPermission->has_access)) {
                        DB::table('role_module_permissions')
                            ->where('id', $existingPermission->id)
                            ->update([
                                'has_access' => $shouldHaveAccess,
                                'updated_at' => now()
                            ]);
                        $permissionsFixed++;
                        
                        $this->line(" - Actualizado permiso para $moduleKey: " . ($shouldHaveAccess ? 'Acceso' : 'Sin acceso'));
                    }
                }
            }
        }
        
        $this->info("Proceso completado.");
        $this->info("Permisos creados: $permissionsCreated");
        $this->info("Permisos actualizados: $permissionsFixed");
        
        return 0;
    }
}
