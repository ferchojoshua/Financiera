<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ForceSuperAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:force-admin {email : Email del usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forzar permisos de superadmin para un usuario específico';

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
        $email = $this->argument('email');
        
        // Buscar al usuario
        $user = DB::table('users')->where('email', $email)->first();
        
        if (!$user) {
            $this->error("No se encontró ningún usuario con el correo: {$email}");
            return 1;
        }
        
        $this->info("Usuario encontrado: {$user->name} ({$user->email})");
        
        // 1. Actualizar el rol y nivel del usuario
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'role' => 'superadmin',
                'level' => 'admin',
                'updated_at' => now()
            ]);
        
        $this->info("Usuario actualizado con rol 'superadmin' y nivel 'admin'");
        
        // 2. Asegurar que tenga todos los permisos en la tabla role_module_permissions
        // Primero, obtener el rol superadmin
        $superadminRole = DB::table('roles')->where('slug', 'superadmin')->first();
        
        if (!$superadminRole) {
            // Crear el rol si no existe
            $superadminId = DB::table('roles')->insertGetId([
                'name' => 'Super Administrador',
                'slug' => 'superadmin',
                'description' => 'Rol con acceso completo al sistema',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->info("Rol superadmin creado con ID: {$superadminId}");
        } else {
            $superadminId = $superadminRole->id;
            $this->info("Rol superadmin encontrado con ID: {$superadminId}");
        }
        
        // 3. Asignar todos los permisos al rol superadmin
        $modules = [
            'dashboard', 'clientes', 'creditos', 'pagos', 'wallet', 'routes', 
            'cobranzas', 'reports', 'configuracion', 'usuarios', 'contabilidad',
            'gastos', 'pymes', 'garantias', 'caja', 'auditoria', 'solicitudes',
            'scoring', 'branches', 'reportes', 'productos', 'admin'
        ];
        
        $count = 0;
        foreach ($modules as $module) {
            DB::table('role_module_permissions')
                ->updateOrInsert(
                    ['role_id' => $superadminId, 'module' => $module],
                    [
                        'has_access' => true,
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );
            $count++;
        }
        
        $this->info("Se han asignado {$count} permisos al rol superadmin");
        
        // 4. Asegurar que el usuario tenga un registro en user_roles si existe esta tabla
        if (Schema::hasTable('user_roles')) {
            DB::table('user_roles')
                ->updateOrInsert(
                    ['user_id' => $user->id],
                    [
                        'role_id' => $superadminId,
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );
            $this->info("Asignado rol superadmin en la tabla user_roles");
        }
        
        $this->info("¡Proceso completado con éxito! El usuario ahora tiene permisos de superadmin.");
        $this->info("Por favor, recarga la página para ver los cambios.");
        
        return 0;
    }
}
