<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Report::create([
            'name' => 'Reporte de Estados Financieros',
            'description' => 'Este reporte muestra los estados financieros de la empresa.',
            'report_type' => 'financial',
            'query_string' => 'SELECT * FROM financial_statements',
            'parameters' => json_encode(['date_from' => ['type' => 'date', 'default' => ''], 'date_to' => ['type' => 'date', 'default' => '']]),
            'output_format' => 'pdf',
            'created_by' => 1,
            'is_public' => true,
            'status' => 'active'
        ]);

        Report::create([
            'name' => 'Reporte de Cartera',
            'description' => 'Este reporte muestra la cartera de créditos activos.',
            'report_type' => 'portfolio',
            'query_string' => 'SELECT * FROM credits WHERE status = "inprogress"',
            'output_format' => 'excel',
            'created_by' => 1,
            'is_public' => false,
            'status' => 'active'
        ]);

        Report::create([
            'name' => 'Reporte de Morosidad',
            'description' => 'Este reporte muestra los créditos con pagos atrasados.',
            'report_type' => 'operational',
            'query_string' => 'SELECT * FROM credits WHERE status = "inprogress" AND next_payment < CURDATE()',
            'output_format' => 'pdf',
            'created_by' => 1,
            'is_public' => true,
            'status' => 'active'
        ]);
    }
} 