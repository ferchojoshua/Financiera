<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowTableStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:structure {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostrar la estructura de una tabla';

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
     * @return mixed
     */
    public function handle()
    {
        $table = $this->argument('table');
        
        $this->info("Estructura de la tabla: {$table}");
        
        try {
            $structure = DB::select("DESCRIBE {$table}");
            
            $headers = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'];
            $rows = [];
            
            foreach ($structure as $column) {
                $rows[] = (array)$column;
            }
            
            $this->table($headers, $rows);
            
            // Mostrar registros
            $this->info("Registros en la tabla {$table}:");
            $records = DB::table($table)->limit(5)->get();
            
            if (count($records) > 0) {
                $headers = array_keys((array)$records[0]);
                $rows = [];
                
                foreach ($records as $record) {
                    $rows[] = (array)$record;
                }
                
                $this->table($headers, $rows);
            } else {
                $this->warn("No hay registros en la tabla {$table}");
            }
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
        
        return 0;
    }
} 