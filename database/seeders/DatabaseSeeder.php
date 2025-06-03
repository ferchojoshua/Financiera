<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Agregar seeders específicos
        $this->call([
            UserSeeder::class,
            WalletSeeder::class,
            AdminUserSeeder::class,
            RoleSeeder::class,
            // Otros seeders
        ]);
    }
} 