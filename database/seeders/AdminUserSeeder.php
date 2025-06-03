<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
    }
}
