<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear usuario super administrador
        User::create([
            'name' => 'Super Admin',
            'last_name' => 'Sistema',
            'email' => 'admin@prestamos.com',
            'password' => Hash::make('admin123'),
            'level' => 'admin',
            'role' => 'superadmin',
            'status' => 'active',
            'phone' => '1234567890',
            'address' => 'Dirección de Administración',
        ]);

        $this->command->info('Usuario Super Admin creado con éxito!');
        $this->command->info('Email: admin@prestamos.com');
        $this->command->info('Contraseña: admin123');
    }
}
