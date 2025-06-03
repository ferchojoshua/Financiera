<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar si ya existen roles
        if (Role::count() > 0) {
            $this->command->info('Ya existen roles en la base de datos.');
            return;
        }
        
        // Crear roles básicos
        $roles = [
            [
                'name' => 'Super Administrador',
                'slug' => 'superadmin',
                'description' => 'Acceso completo a todas las funcionalidades del sistema'
            ],
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Acceso a la mayoría de las funcionalidades administrativas'
            ],
            [
                'name' => 'Supervisor',
                'slug' => 'supervisor',
                'description' => 'Supervisa el trabajo de los agentes y gestiona rutas'
            ],
            [
                'name' => 'Caja',
                'slug' => 'caja',
                'description' => 'Gestiona pagos y desembolsos'
            ],
            [
                'name' => 'Colector',
                'slug' => 'colector',
                'description' => 'Realiza cobranza en ruta y registra pagos'
            ],
            [
                'name' => 'Cliente',
                'slug' => 'user',
                'description' => 'Usuario normal que puede tener préstamos'
            ],
        ];
        
        foreach ($roles as $role) {
            Role::create($role);
        }
        
        $this->command->info('Roles creados correctamente.');
    }
} 