<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DatabaseFixController extends Controller
{
    /**
     * Verificar y crear la tabla payments si no existe
     */
    public function fixPaymentsTable()
    {
        try {
            // Verificar si la tabla payments existe
            if (Schema::hasTable('payments')) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'La tabla payments ya existe'
                ]);
            }

            // Crear la tabla payments
            Log::info('Creando tabla payments desde DatabaseFixController');
            
            Schema::create('payments', function ($table) {
                $table->id();
                $table->unsignedBigInteger('credit_id');
                $table->decimal('amount', 10, 2);
                $table->dateTime('payment_date')->nullable();
                $table->date('due_date')->nullable();
                $table->string('payment_method')->default('cash');
                $table->string('reference_number')->nullable();
                $table->string('status')->default('pending');
                $table->unsignedBigInteger('collected_by')->nullable();
                $table->text('notes')->nullable();
                $table->integer('installment_number')->nullable();
                $table->decimal('late_fee', 10, 2)->default(0);
                $table->timestamps();

                // Índices
                $table->index('credit_id');
                $table->index('status');
                $table->index('due_date');
                $table->index('payment_date');
                $table->index('collected_by');
            });

            // Intentar agregar foreign keys
            try {
                Schema::table('payments', function ($table) {
                    if (Schema::hasTable('credit')) {
                        $table->foreign('credit_id')->references('id')->on('credit')->onDelete('cascade');
                    }
                    
                    if (Schema::hasTable('users')) {
                        $table->foreign('collected_by')->references('id')->on('users')->onDelete('set null');
                    }
                });
            } catch (\Exception $e) {
                Log::warning('No se pudieron agregar las claves foráneas: ' . $e->getMessage());
            }

            // Migrar datos de summary si existe
            if (Schema::hasTable('summary')) {
                try {
                    DB::statement("
                        INSERT INTO payments (credit_id, amount, payment_date, collected_by, created_at, updated_at)
                        SELECT id_credit, amount, created_at, id_agent, created_at, NOW()
                        FROM summary
                    ");
                    
                    $count = DB::table('summary')->count();
                    Log::info("Se migraron {$count} registros de summary a payments");
                } catch (\Exception $e) {
                    Log::error('Error al migrar datos: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Se ha creado la tabla payments exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear la tabla payments: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la tabla payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vista para corregir problemas de base de datos
     */
    public function fixDatabaseView()
    {
        $tablesStatus = [
            'payments' => Schema::hasTable('payments'),
            'summary' => Schema::hasTable('summary'),
            'credit' => Schema::hasTable('credit'),
            'users' => Schema::hasTable('users'),
        ];
        
        return view('admin.database_fix', compact('tablesStatus'));
    }

    /**
     * Ejecutar migración específica
     */
    public function runMigration(Request $request)
    {
        $migration = $request->input('migration');
        
        if (!$migration) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se especificó la migración a ejecutar'
            ], 400);
        }
        
        try {
            Artisan::call('migrate', [
                '--path' => "database/migrations/{$migration}.php",
                '--force' => true
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Migración ejecutada correctamente',
                'output' => Artisan::output()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al ejecutar la migración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ejecutar comando personalizado
     */
    public function runCommand(Request $request)
    {
        $command = $request->input('command');
        
        if (!$command) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se especificó el comando a ejecutar'
            ], 400);
        }
        
        try {
            Artisan::call($command);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Comando ejecutado correctamente',
                'output' => Artisan::output()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al ejecutar el comando: ' . $e->getMessage()
            ], 500);
        }
    }
} 