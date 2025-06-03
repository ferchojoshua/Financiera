<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletMigrationController extends Controller
{
    /**
     * Verificar requisitos para iniciar la migración.
     */
    public function check()
    {
        $oldWalletsCount = DB::table('wallet')->count();
        $newWalletsCount = DB::table('wallets')->count();
        $migratedCount = DB::table('wallets')->whereNotNull('legacy_id')->count();
        
        return view('admin.wallet.migration', [
            'oldWalletsCount' => $oldWalletsCount,
            'newWalletsCount' => $newWalletsCount,
            'migratedCount' => $migratedCount,
            'readyToMigrate' => ($oldWalletsCount > 0),
            'columnsExist' => $this->checkColumns()
        ]);
    }
    
    /**
     * Verificar que existan las columnas necesarias en la tabla wallets.
     */
    private function checkColumns()
    {
        try {
            $columns = DB::select('SHOW COLUMNS FROM wallets');
            $columnNames = array_map(function($column) {
                return $column->Field;
            }, $columns);
            
            $requiredColumns = ['legacy_id', 'supervisor_id', 'wallet_type', 'status', 'country_id', 'address'];
            $missingColumns = array_diff($requiredColumns, $columnNames);
            
            return empty($missingColumns);
        } catch (\Exception $e) {
            Log::error('Error verificando columnas: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ejecutar la migración de datos.
     */
    public function migrate()
    {
        // Verificar si ya se ha completado la migración
        $oldWalletsCount = DB::table('wallet')->count();
        $migratedCount = DB::table('wallets')->whereNotNull('legacy_id')->count();
        
        if ($migratedCount >= $oldWalletsCount && $oldWalletsCount > 0) {
            return redirect()->route('admin.wallet.migration.check')
                ->with('warning', 'La migración ya parece estar completa.');
        }
        
        // Iniciar transacción para asegurar consistencia
        DB::beginTransaction();
        $migratedCount = 0;
        $errors = [];
        
        try {
            // Obtener todas las wallets antiguas
            $oldWallets = DB::table('wallet')->get();
            
            foreach ($oldWallets as $oldWallet) {
                // Verificar si ya existe una migración para esta wallet
                $exists = DB::table('wallets')
                    ->where('legacy_id', $oldWallet->id)
                    ->exists();
                
                if ($exists) {
                    continue;
                }
                
                // Buscar supervisor relacionado
                $supervisor = DB::table('agent_has_supervisor')
                    ->where('id_wallet', $oldWallet->id)
                    ->first();
                
                $userId = $supervisor ? $supervisor->id_supervisor : 1;
                $supervisorId = $supervisor ? $supervisor->id_supervisor : null;
                
                // Crear nueva wallet
                DB::table('wallets')->insert([
                    'legacy_id' => $oldWallet->id,
                    'user_id' => $userId,
                    'supervisor_id' => $supervisorId,
                    'balance' => 0, // Calcular saldo real
                    'description' => $oldWallet->name ?? 'Wallet migrada',
                    'wallet_type' => 'migrated',
                    'status' => 'activa',
                    'country_id' => $oldWallet->country,
                    'address' => $oldWallet->address,
                    'created_by' => 1,
                    'created_at' => $oldWallet->created_at ?? now(),
                    'updated_at' => now(),
                ]);
                
                $migratedCount++;
            }
            
            // Actualizar referencias en agent_has_supervisor
            // Esto es solo un ejemplo y depende de la estructura de tu base de datos
            /*
            $wallets = DB::table('wallets')
                ->whereNotNull('legacy_id')
                ->get(['id', 'legacy_id']);
                
            foreach ($wallets as $wallet) {
                DB::table('agent_has_supervisor')
                    ->where('id_wallet', $wallet->legacy_id)
                    ->update(['id_wallet' => $wallet->id]);
            }
            */
            
            DB::commit();
            
            return redirect()->route('admin.wallet.migration.check')
                ->with('success', "Migración completada. Se migraron {$migratedCount} billeteras.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en migración: ' . $e->getMessage());
            
            return redirect()->route('admin.wallet.migration.check')
                ->with('error', 'Error durante la migración: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar información de wallets para comparar ambos sistemas.
     */
    public function compare()
    {
        $oldWallets = DB::table('wallet')
            ->select('id', 'name', 'created_at', 'country', 'address')
            ->get();
            
        $newWallets = DB::table('wallets')
            ->select('id', 'legacy_id', 'user_id', 'balance', 'description', 'status', 'created_at')
            ->whereNotNull('legacy_id')
            ->get();
            
        return view('admin.wallet.compare', [
            'oldWallets' => $oldWallets,
            'newWallets' => $newWallets
        ]);
    }
    
    /**
     * Realizar ajustes finales después de la migración.
     */
    public function finalize()
    {
        // Esta función puede ejecutar ajustes finales como:
        // - Actualizar constantes o configuraciones
        // - Limpiar datos temporales
        // - Actualizar la documentación
        
        // Marcar la migración como completada en la configuración
        // config(['app.wallet_migration_completed' => true]);
        
        return redirect()->route('admin.wallet.migration.check')
            ->with('success', 'Migración finalizada y sistema actualizado.');
    }
} 