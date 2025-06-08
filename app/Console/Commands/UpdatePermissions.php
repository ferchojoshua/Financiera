<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los permisos de módulos para todos los roles';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Actualizando permisos...');

        try {
            // Obtener todos los roles
            $roles = DB::table('roles')->get();

            foreach ($roles as $role) {
                $isAdmin = in_array($role->slug, ['admin', 'superadmin']);

                // Actualizar todos los permisos existentes para admin/superadmin
                if ($isAdmin) {
                    DB::table('role_module_permissions')
                        ->where('role_id', $role->id)
                        ->update([
                            'has_access' => true,
                            'updated_at' => now()
                        ]);

                    $this->info("Permisos actualizados para {$role->name}");
                }
            }

            $this->info('¡Permisos actualizados correctamente!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error al actualizar permisos: ' . $e->getMessage());
            return 1;
        }
    }
}
