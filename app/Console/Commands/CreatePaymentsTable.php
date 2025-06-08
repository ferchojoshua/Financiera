<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:create-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea la tabla payments si no existe y migra datos de summary';

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
        try {
            // Verificar si la tabla ya existe
            if (Schema::hasTable('payments')) {
                $this->error('La tabla payments ya existe!');
                return 1;
            }

            $this->info('Creando tabla payments...');
            
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

            // Agregar foreign keys
            Schema::table('payments', function ($table) {
                // Verificar si las tablas existen antes de agregar las claves foráneas
                if (Schema::hasTable('credit')) {
                    try {
                        $table->foreign('credit_id')->references('id')->on('credit')->onDelete('cascade');
                    } catch (\Exception $e) {
                        $this->warn('No se pudo agregar la clave foránea para credit_id: ' . $e->getMessage());
                    }
                }

                if (Schema::hasTable('users')) {
                    try {
                        $table->foreign('collected_by')->references('id')->on('users')->onDelete('set null');
                    } catch (\Exception $e) {
                        $this->warn('No se pudo agregar la clave foránea para collected_by: ' . $e->getMessage());
                    }
                }
            });

            $this->info('Tabla payments creada correctamente');

            // Migrar datos de summary si existe
            if (Schema::hasTable('summary')) {
                $this->info('Migrando datos desde la tabla summary...');
                
                try {
                    $count = DB::table('summary')->count();
                    
                    if ($count > 0) {
                        DB::statement("
                            INSERT INTO payments (credit_id, amount, payment_date, collected_by, created_at, updated_at)
                            SELECT id_credit, amount, created_at, id_agent, created_at, NOW()
                            FROM summary
                        ");
                        
                        $this->info("Se migraron {$count} registros de summary a payments");
                    } else {
                        $this->info('La tabla summary está vacía, no hay datos para migrar');
                    }
                } catch (\Exception $e) {
                    $this->error('Error al migrar datos: ' . $e->getMessage());
                }
            } else {
                $this->info('La tabla summary no existe, no se migraron datos');
            }

            $this->info('Proceso completado con éxito');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
} 