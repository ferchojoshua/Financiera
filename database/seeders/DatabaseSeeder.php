<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            CreditTypeSeeder::class,
            // Otros seeders
        ]);
    }
} 