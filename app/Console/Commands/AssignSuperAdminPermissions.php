<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class AssignSuperAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:superadmin-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna todos los permisos necesarios al rol de superadmin';

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
        $this->info('Iniciando asignación de permisos para superadmin...');

        // 1. Asegurar que existe el rol superadmin
        $superadminRole = Role::where('slug', 'superadmin')->first();
        if (!$superadminRole) {
            $superadminRole = Role::create([
                'name' => 'Super Administrador',
                'slug' => 'superadmin',
                'description' => 'Rol con acceso total al sistema',
                'is_active' => true
            ]);
            $this->info('Rol superadmin creado.');
        }

        // 2. Lista de todos los módulos del sistema
        $modules = [
            'dashboard',
            'clientes',
            'pymes',
            'creditos',
            'pagos',
            'cobranzas',
            'garantias',
            'reportes',
            'contabilidad',
            'productos',
            'configuracion',
            'usuarios',
            'sucursales',
            'billeteras',
            'simulador',
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
        
        // 3. Eliminar permisos antiguos para superadmin
        DB::table('role_module_permissions')
            ->where('role_id', $superadminRole->id)
            ->delete();
            
        // 4. Asignar todos los módulos
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

        // 5. Actualizar usuarios superadmin
        $superadminUsers = User::where('role', 'superadmin')
            ->orWhere('level', 'admin')
            ->get();

        foreach ($superadminUsers as $user) {
            $user->role = 'superadmin';
            $user->level = 'admin';
            $user->save();

            // Asignar rol en la tabla user_roles si existe
            if (Schema::hasTable('user_roles')) {
                DB::table('user_roles')
                    ->updateOrInsert(
                        ['user_id' => $user->id],
                        [
                            'role_id' => $superadminRole->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
            }
        }

        $this->info("Se asignaron {$count} permisos al rol superadmin.");
        $this->info("Se actualizaron " . $superadminUsers->count() . " usuarios superadmin.");
        
        return 0;
    }
} 