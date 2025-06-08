<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ManagePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:manage
                            {action? : Action to perform (list, assign, revoke, sync)}
                            {--role= : Role slug to manage permissions for}
                            {--module= : Module to assign or revoke access to}
                            {--all : Apply to all modules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage role-based permissions for modules';

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
        $action = $this->argument('action') ?: 'list';
        
        switch ($action) {
            case 'list':
                return $this->listPermissions();
            case 'assign':
                return $this->assignPermission();
            case 'revoke':
                return $this->revokePermission();
            case 'sync':
                return $this->syncPermissions();
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }
    
    /**
     * List permissions for roles
     */
    protected function listPermissions()
    {
        $roleSlug = $this->option('role');
        
        // Si se especifica un rol, mostrar sus permisos
        if ($roleSlug) {
            $role = DB::table('roles')->where('slug', $roleSlug)->first();
            
            if (!$role) {
                $this->error("Role not found: {$roleSlug}");
                return 1;
            }
            
            $permissions = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->orderBy('module')
                ->get();
                
            $this->info("Permissions for role: {$role->name} ({$role->slug})");
            
            $headers = ['Module', 'Has Access'];
            $rows = [];
            
            foreach ($permissions as $permission) {
                $rows[] = [
                    $permission->module,
                    $permission->has_access ? 'Yes' : 'No'
                ];
            }
            
            $this->table($headers, $rows);
            
        } else {
            // Mostrar todos los roles y sus permisos
            $roles = DB::table('roles')->where('is_active', 1)->orderBy('name')->get();
            
            $headers = ['Role', 'Modules with Access'];
            $rows = [];
            
            foreach ($roles as $role) {
                $modules = DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('has_access', 1)
                    ->pluck('module')
                    ->toArray();
                    
                $rows[] = [
                    "{$role->name} ({$role->slug})",
                    implode(', ', $modules)
                ];
            }
            
            $this->table($headers, $rows);
        }
        
        return 0;
    }
    
    /**
     * Assign permission to a role
     */
    protected function assignPermission()
    {
        $roleSlug = $this->option('role');
        $module = $this->option('module');
        $all = $this->option('all');
        
        if (!$roleSlug) {
            $this->error('Role is required (--role=slug)');
            return 1;
        }
        
        if (!$module && !$all) {
            $this->error('Module is required (--module=name) or use --all for all modules');
            return 1;
        }
        
        $role = DB::table('roles')->where('slug', $roleSlug)->first();
        
        if (!$role) {
            $this->error("Role not found: {$roleSlug}");
            return 1;
        }
        
        if ($all) {
            // Asignar acceso a todos los módulos
            $modules = DB::table('role_module_permissions')
                ->select('module')
                ->distinct()
                ->pluck('module')
                ->toArray();
                
            if (empty($modules)) {
                $this->info('No modules found in the database');
                return 0;
            }
            
            foreach ($modules as $mod) {
                DB::table('role_module_permissions')
                    ->updateOrInsert(
                        ['role_id' => $role->id, 'module' => $mod],
                        ['has_access' => true, 'updated_at' => now()]
                    );
            }
            
            $this->info("Assigned access to all modules for role: {$role->name}");
            
        } else {
            // Asignar acceso a un módulo específico
            DB::table('role_module_permissions')
                ->updateOrInsert(
                    ['role_id' => $role->id, 'module' => $module],
                    ['has_access' => true, 'updated_at' => now()]
                );
                
            $this->info("Assigned access to module '{$module}' for role: {$role->name}");
        }
        
        return 0;
    }
    
    /**
     * Revoke permission from a role
     */
    protected function revokePermission()
    {
        $roleSlug = $this->option('role');
        $module = $this->option('module');
        $all = $this->option('all');
        
        if (!$roleSlug) {
            $this->error('Role is required (--role=slug)');
            return 1;
        }
        
        if (!$module && !$all) {
            $this->error('Module is required (--module=name) or use --all for all modules');
            return 1;
        }
        
        $role = DB::table('roles')->where('slug', $roleSlug)->first();
        
        if (!$role) {
            $this->error("Role not found: {$roleSlug}");
            return 1;
        }
        
        if ($all) {
            // Revocar acceso a todos los módulos
            DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->update(['has_access' => false, 'updated_at' => now()]);
                
            $this->info("Revoked access to all modules for role: {$role->name}");
            
        } else {
            // Revocar acceso a un módulo específico
            DB::table('role_module_permissions')
                ->updateOrInsert(
                    ['role_id' => $role->id, 'module' => $module],
                    ['has_access' => false, 'updated_at' => now()]
                );
                
            $this->info("Revoked access to module '{$module}' for role: {$role->name}");
        }
        
        return 0;
    }
    
    /**
     * Sync permissions from the configuration
     */
    protected function syncPermissions()
    {
        // Definir los módulos del sistema y sus permisos por rol
        $modulePermissions = [
            // Administrador - tiene acceso a todos los módulos
            'admin' => [
                'pymes' => true,
                'seguridad' => true,
                'rutas' => true,
                'caja' => true,
                'wallet' => true,
                'billetera' => true,
                'garantias' => true,
                'simulador' => true,
                'cobranza' => true,
                'auditoria' => true,
                'empresa' => true,
                'permisos' => true,
                'preferencias' => true,
                'reportes_cancelados' => true,
                'reportes_desembolsos' => true,
                'reportes_activos' => true,
                'reportes_vencidos' => true,
                'reportes_por_cancelar' => true,
                'solicitudes' => true,
                'analisis' => true,
                'productos' => true,
                'acuerdos' => true,
                'cierre_mes' => true,
                'recuperacion_desembolsos' => true,
                'asignacion_creditos' => true,
                'dashboard' => true,
                'usuarios' => true,
                'clientes' => true,
                'pagos' => true,
                'reportes' => true,
            ],
            
            // Supervisor - acceso a módulos de supervisión
            'supervisor' => [
                'rutas' => true,
                'caja' => false,
                'cobranza' => true,
                'reportes' => true,
                'usuarios' => true,
                'clientes' => true,
                'creditos' => true,
                'pagos' => true,
                'auditoria' => false,
                'dashboard' => true,
            ],
            
            // Caja - acceso a módulos de caja
            'caja' => [
                'caja' => true,
                'pagos' => true,
                'reportes_diarios' => true,
                'dashboard' => true,
            ],
            
            // Colector - acceso a módulos de cobranza
            'Colector' => [
                'cobranza' => true,
                'clientes' => true,
                'creditos' => true,
                'pagos' => true,
                'rutas' => true,
                'dashboard' => true,
            ],
            
            // Contador - acceso a módulos de contabilidad
            'contador' => [
                'contabilidad' => true,
                'reportes' => true,
                'auditoria' => true,
                'dashboard' => true,
            ],
            
            // Cliente - acceso básico
            'user' => [
                'creditos_propios' => true,
                'pagos_propios' => true,
                'perfil' => true,
                'dashboard' => true,
            ]
        ];
        
        // Obtener todos los roles
        $roles = [];
        $rolesResult = DB::table('roles')->where('is_active', 1)->get();
        foreach ($rolesResult as $role) {
            $roles[$role->slug] = $role->id;
        }
        
        // Sincronizar permisos para cada rol
        $this->info("Syncing permissions for roles:");
        
        foreach ($modulePermissions as $roleSlug => $modules) {
            // Verificar si el rol existe
            if (!isset($roles[$roleSlug])) {
                $this->warn("- Role '{$roleSlug}' not found, skipping.");
                continue;
            }
            
            $roleId = $roles[$roleSlug];
            $this->info("- {$roleSlug}");
            
            // Configurar permisos para cada módulo
            foreach ($modules as $module => $hasAccess) {
                DB::table('role_module_permissions')
                    ->updateOrInsert(
                        ['role_id' => $roleId, 'module' => $module],
                        ['has_access' => $hasAccess, 'updated_at' => now()]
                    );
                    
                $this->line("  - Module '{$module}': " . ($hasAccess ? 'granted' : 'denied'));
            }
        }
        
        $this->info("\nPermissions synced successfully");
        return 0;
    }
} 