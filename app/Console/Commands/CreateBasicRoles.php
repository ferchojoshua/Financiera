<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateBasicRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:create-basic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea los roles básicos del sistema';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Creando roles básicos del sistema...');

        // Definimos los roles básicos
        $roles = [
            [
                'name' => 'Super Administrador',
                'slug' => 'superadmin',
                'description' => 'Tiene acceso completo a todo el sistema',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Administrador general del sistema',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Agente',
                'slug' => 'agente',
                'description' => 'Agente de crédito',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Supervisor',
                'slug' => 'supervisor',
                'description' => 'Supervisor de agentes',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($roles as $role) {
            // Verificamos si ya existe el rol
            $exists = DB::table('roles')->where('slug', $role['slug'])->exists();
            
            if (!$exists) {
                DB::table('roles')->insert($role);
                $this->info("- Rol '{$role['name']}' creado correctamente");
            } else {
                $this->info("- Rol '{$role['name']}' ya existe");
            }
        }

        $this->info('Roles básicos creados correctamente.');
        return 0;
    }
} 