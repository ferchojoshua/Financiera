<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicatePermissions extends Command
{
    protected $signature = 'permissions:clean-duplicates';
    protected $description = 'Limpia permisos duplicados en la tabla role_module_permissions';

    public function handle()
    {
        $this->info('Iniciando limpieza de permisos duplicados...');

        try {
            // Crear tabla temporal con los registros únicos que queremos mantener
            DB::statement('
                CREATE TEMPORARY TABLE temp_unique_permissions AS
                SELECT MAX(id) as id
                FROM role_module_permissions
                GROUP BY role_id, module
            ');

            // Contar cuántos registros se eliminarán
            $duplicatesCount = DB::table('role_module_permissions')
                ->whereNotIn('id', function($query) {
                    $query->select('id')
                        ->from('temp_unique_permissions');
                })
                ->count();

            if ($duplicatesCount > 0) {
                // Eliminar los duplicados
                DB::table('role_module_permissions')
                    ->whereNotIn('id', function($query) {
                        $query->select('id')
                            ->from('temp_unique_permissions');
                    })
                    ->delete();

                $this->info("Se eliminaron {$duplicatesCount} registros duplicados.");
            } else {
                $this->info('No se encontraron registros duplicados.');
            }

            // Eliminar tabla temporal
            DB::statement('DROP TEMPORARY TABLE IF EXISTS temp_unique_permissions');

            // Verificar que el índice único existe
            $hasUniqueIndex = DB::select("
                SHOW INDEXES FROM role_module_permissions 
                WHERE Key_name = 'unique_role_module'
            ");

            if (empty($hasUniqueIndex)) {
                // Agregar índice único si no existe
                DB::statement('
                    ALTER TABLE role_module_permissions 
                    ADD CONSTRAINT unique_role_module 
                    UNIQUE (role_id, module)
                ');
                $this->info('Se agregó el índice único para prevenir futuros duplicados.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error durante la limpieza: ' . $e->getMessage());
            return 1;
        }
    }
} 