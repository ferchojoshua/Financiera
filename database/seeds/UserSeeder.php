<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $faker;

    public function run()
    {
        $this->faker = $faker = Faker\Factory::create();
        $users = array(
            [
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'level' => 'admin',
                'password' => Hash::make('admin'),
                'status' => 'enabled',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Supervisor',
                'email' => 'supervisor@supervisor.com',
                'level' => 'supervisor',
                'password' => Hash::make('12345678'),
                'status' => 'enabled',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Agente',
                'email' => 'agente@agente.com',
                'level' => 'agent',
                'password' => Hash::make('12345678'),
                'status' => 'enabled',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}