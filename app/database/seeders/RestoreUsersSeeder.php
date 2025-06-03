<?php

namespace App\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\User;

class RestoreUsersSeeder extends Seeder
{
    /**
     * Restaurar usuarios de prueba.
     *
     * @return void
     */
    public function run()
    {
        // Verificar si ya existen usuarios para no duplicar
        if (User::count() > 0) {
            $this->command->info('Ya existen usuarios en la base de datos. No se restaurarán para evitar duplicados.');
            return;
        }

        // Crear usuario superadmin
        User::create([
            'name' => 'Super Admin',
            'last_name' => 'Sistema',
            'email' => 'admin@prestamos.com',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'nit' => '1234567890',
            'phone' => '1234567890',
            'role' => 'superadmin',
            'level' => '1',
            'active_user' => 1,
        ]);

        // Crear usuario admin
        User::create([
            'name' => 'Administrador',
            'last_name' => 'General',
            'email' => 'admin2@prestamos.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'nit' => '0987654321',
            'phone' => '0987654321',
            'role' => 'admin',
            'level' => '1',
            'active_user' => 1,
        ]);

        // Crear usuario supervisor
        User::create([
            'name' => 'Supervisor',
            'last_name' => 'De Zona',
            'email' => 'supervisor@prestamos.com',
            'username' => 'supervisor',
            'password' => Hash::make('password'),
            'nit' => '1122334455',
            'phone' => '1122334455',
            'role' => 'supervisor',
            'level' => '1',
            'active_user' => 1,
        ]);

        // Crear usuario agente
        User::create([
            'name' => 'Agente',
            'last_name' => 'De Campo',
            'email' => 'agente@prestamos.com',
            'username' => 'agente',
            'password' => Hash::make('password'),
            'nit' => '5566778899',
            'phone' => '5566778899',
            'role' => 'agent',
            'level' => '1',
            'active_user' => 1,
        ]);

        $this->command->info('Usuarios restaurados con éxito.');
    }
} 