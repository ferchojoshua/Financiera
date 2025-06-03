<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class CreateAdminWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallets:create-admin {--force : Force creation even if wallets exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create wallets for admin and superadmin users who do not have one';

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
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for admin users without wallets...');
        
        // Obtener todos los usuarios admin y superadmin
        $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
        
        if ($admins->isEmpty()) {
            $this->warn('No admin users found in the system.');
            return 0;
        }
        
        $this->info('Found ' . $admins->count() . ' admin users.');
        
        $created = 0;
        $skipped = 0;
        $errors = 0;
        
        $force = $this->option('force');
        
        foreach ($admins as $admin) {
            try {
                // Verificar si ya tiene billetera
                $existingWallet = Wallet::where('user_id', $admin->id)->first();
                
                if ($existingWallet && !$force) {
                    $this->line("<fg=yellow>Skipped:</> User {$admin->name} already has a wallet (ID: {$existingWallet->id}).");
                    $skipped++;
                    continue;
                }
                
                if ($existingWallet && $force) {
                    $this->line("<fg=yellow>Force:</> Recreating wallet for {$admin->name}.");
                    $existingWallet->delete();
                }
                
                DB::beginTransaction();
                
                // Crear billetera
                $wallet = new Wallet();
                $wallet->user_id = $admin->id;
                $wallet->balance = 0;
                $wallet->description = "Billetera administrativa generada automáticamente";
                $wallet->created_by = $admin->id; // El usuario es el creador de su propia billetera
                $wallet->save();
                
                DB::commit();
                
                $this->info("<fg=green>Created:</> Wallet for {$admin->name} (ID: {$wallet->id})");
                $created++;
                
            } catch (\Exception $e) {
                DB::rollback();
                $this->error("<fg=red>Error:</> Could not create wallet for {$admin->name}: {$e->getMessage()}");
                $errors++;
            }
        }
        
        $this->newLine();
        $this->line("Summary:");
        $this->line("- Created: $created");
        $this->line("- Skipped: $skipped");
        $this->line("- Errors: $errors");
        
        return 0;
    }
} 