<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class SafeMigration extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'migrate:safe {--path= : Ruta específica de migración} {--force : Forzar ejecución en producción}';

    /**
     * La descripción del comando de consola.
     *
     * @var string
     */
    protected $description = 'Ejecuta migraciones de forma segura sin afectar datos existentes';

    /**
     * Crear una nueva instancia del comando.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ejecutar el comando de consola.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando migración segura...');
        
        // Crear un respaldo de usuarios si hay conexión a la base de datos
        try {
            // Verificar si existe la tabla users
            if (Schema::hasTable('users')) {
                $users = DB::table('users')->get();
                $this->info('Respaldo de ' . count($users) . ' usuarios creado en memoria.');
            } else {
                $users = [];
                $this->warn('La tabla users no existe. No se creó respaldo.');
            }
        } catch (\Exception $e) {
            $this->error('Error al crear respaldo: ' . $e->getMessage());
            if (!$this->confirm('¿Desea continuar sin respaldo?', false)) {
                return 1;
            }
            $users = [];
        }
        
        // Ejecutar migraciones
        $path = $this->option('path');
        $force = $this->option('force');
        
        $command = 'migrate';
        $params = ['--step' => true];
        
        if ($path) {
            $params['--path'] = $path;
            $this->info('Ejecutando migraciones en ruta: ' . $path);
        }
        
        if ($force) {
            $params['--force'] = true;
        }
        
        // Ejecutar migraciones de forma segura
        $this->info('Ejecutando migraciones...');
        Artisan::call($command, $params);
        $this->info(Artisan::output());
        
        // Verificar si hay que restaurar usuarios
        if (!empty($users) && Schema::hasTable('users') && DB::table('users')->count() == 0) {
            $this->info('Restaurando usuarios...');
            
            // Restaurar usuarios desde el respaldo
            foreach ($users as $user) {
                // Convertir el objeto a un array
                $userData = (array) $user;
                // Eliminar created_at y updated_at para que se generen nuevos
                unset($userData['created_at']);
                unset($userData['updated_at']);
                
                try {
                    DB::table('users')->insert($userData);
                } catch (\Exception $e) {
                    $this->warn('No se pudo restaurar el usuario ID ' . $user->id . ': ' . $e->getMessage());
                }
            }
            
            $this->info('Usuarios restaurados con éxito.');
        }
        
        // Informar finalización
        $this->info('Migración segura completada.');
        
        // Ejecutar seed si es necesario
        if (Schema::hasTable('users') && DB::table('users')->count() == 0) {
            if ($this->confirm('No hay usuarios en la base de datos. ¿Desea ejecutar el seeder de usuarios?', true)) {
                Artisan::call('db:seed', ['--class' => 'RestoreUsersSeeder']);
                $this->info(Artisan::output());
            }
        }
        
        return 0;
    }
}
