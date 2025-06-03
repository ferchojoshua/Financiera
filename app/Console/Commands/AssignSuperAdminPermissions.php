<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignSuperAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar todos los permisos de módulos al rol superadmin';

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
        $this->info('Asignando permisos completos al superadmin...');
        
        // Buscar el rol superadmin
        $superadminRole = DB::table('roles')->where('slug', 'superadmin')->first();
        
        if (!$superadminRole) {
            $this->error('El rol superadmin no existe en la base de datos.');
            return 1;
        }
        
        // Módulos del sistema (asegúrate de incluir todos)
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
            'billeteras',
            'simulador',
            'pymes',
            'garantias',
            'productos',
            'rutas',
            'auditoria',
            'empresa',
            'billetera',
            'solicitudes',
            'analisis',
            'acuerdos',
            'reportes_cancelados',
            'reportes_desembolsos',
            'reportes_activos',
            'reportes_vencidos',
            'reportes_por_cancelar',
            'cierre_mes',
            'recuperacion_desembolsos',
            'permisos',
            'preferencias',
            'clientes_regular',
            'clients_pyme',
            'credit_requests',
            'scoring',
            'guarantees',
            'financial_products',
            'payment_agreements',
            'collections',
            'payments',
            'canceled_reports',
            'wallet'
        ];
        
        $count = 0;
        
        // Eliminar permisos antiguos para superadmin
        DB::table('role_module_permissions')
            ->where('role_id', $superadminRole->id)
            ->delete();
            
        // Asignar todos los módulos
        foreach ($modules as $module) {
            DB::table('role_module_permissions')->insert([
                'role_id' => $superadminRole->id,
                'module' => $module,
                'has_access' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $count++;
        }
        
        // Verificar si existe la columna 'role' en la tabla users
        $hasRoleColumn = false;
        $columns = DB::getSchemaBuilder()->getColumnListing('users');
        if (in_array('role', $columns)) {
            $hasRoleColumn = true;
            // Actualizar usuarios superadmin para asegurar que tienen el nivel correcto
            DB::table('users')
                ->where('role', 'superadmin')
                ->update(['level' => 'superadmin']);
                
            DB::table('users')
                ->where('level', 'superadmin')
                ->update(['role' => 'superadmin']);
                
            $this->info("Se actualizaron los usuarios superadmin con level y role correctos.");
        } else {
            $this->info("La columna 'role' no existe en la tabla users. No se actualizaron los usuarios.");
        }
        
        $this->info("Se asignaron $count permisos al rol superadmin.");
        
        return 0;
    }
} 