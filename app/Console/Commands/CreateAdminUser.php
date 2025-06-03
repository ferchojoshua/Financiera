<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the default admin user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::table('users')->insert([
                'name' => 'Administrador',
                'email' => 'admin@prestamos.com',
                'password' => Hash::make('12345678'),
                'level' => 'admin',
                'active_user' => 'enabled',
                'status' => 'good',
                'nit' => 'ADMIN001',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->info('Usuario administrador creado exitosamente.');
        } catch (\Exception $e) {
            $this->error('Error al crear el usuario administrador: ' . $e->getMessage());
        }
    }
}
