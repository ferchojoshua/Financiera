<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Creating wallets for admin users...');
        
        // Asegurarse de que haya al menos un superadmin
        $superadmin = User::where('role', 'superadmin')->first();
        
        if (!$superadmin) {
            $this->command->warn('No superadmin found. Creating a default one...');
            
            $superadmin = User::create([
                'name' => 'Super Administrador',
                'email' => 'admin@sistema.com',
                'password' => bcrypt('admin123'),
                'role' => 'superadmin',
            ]);
            
            $this->command->info('Created superadmin: ' . $superadmin->email);
        }
        
        // Crear billetera para el superadmin si no tiene
        $wallet = Wallet::where('user_id', $superadmin->id)->first();
        
        if (!$wallet) {
            DB::beginTransaction();
            
            try {
                $wallet = Wallet::create([
                    'user_id' => $superadmin->id,
                    'balance' => 10000,
                    'description' => 'Billetera principal del sistema',
                    'created_by' => $superadmin->id,
                ]);
                
                // Registrar transacción de depósito inicial
                Transaction::create([
                    'wallet_id' => $wallet->id,
                    'amount' => 10000,
                    'type' => 'deposit',
                    'description' => 'Saldo inicial',
                    'created_by' => $superadmin->id,
                ]);
                
                DB::commit();
                
                $this->command->info('Created wallet for superadmin with $10,000 initial balance');
            } catch (\Exception $e) {
                DB::rollback();
                $this->command->error('Error creating wallet: ' . $e->getMessage());
            }
        } else {
            $this->command->info('Superadmin already has a wallet');
        }
        
        // Obtener todos los usuarios admin y crear billeteras
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $existingWallet = Wallet::where('user_id', $admin->id)->first();
            
            if (!$existingWallet) {
                try {
                    DB::beginTransaction();
                    
                    $adminWallet = Wallet::create([
                        'user_id' => $admin->id,
                        'balance' => 5000,
                        'description' => 'Billetera de administración',
                        'created_by' => $superadmin->id,
                    ]);
                    
                    // Registrar transacción de depósito inicial
                    Transaction::create([
                        'wallet_id' => $adminWallet->id,
                        'amount' => 5000,
                        'type' => 'deposit',
                        'description' => 'Saldo inicial',
                        'created_by' => $superadmin->id,
                    ]);
                    
                    DB::commit();
                    
                    $this->command->info('Created wallet for admin ' . $admin->name);
                } catch (\Exception $e) {
                    DB::rollback();
                    $this->command->error('Error creating wallet for ' . $admin->name . ': ' . $e->getMessage());
                }
            } else {
                $this->command->line('Admin ' . $admin->name . ' already has a wallet');
            }
        }
    }
} 