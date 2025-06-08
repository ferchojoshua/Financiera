<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditType;

class CreditTypeSeeder extends Seeder
{
    public function run()
    {
        $creditTypes = [
            [
                'name' => 'Crédito Personal',
                'description' => 'Crédito para gastos personales con tasa preferencial',
                'min_amount' => 500,
                'max_amount' => 5000,
                'interest_rate' => 24.00, // 24% anual
                'min_term_months' => 3,
                'max_term_months' => 24,
                'requires_guarantee' => false,
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Crédito de Negocio',
                'description' => 'Financiamiento para capital de trabajo y expansión',
                'min_amount' => 5000,
                'max_amount' => 50000,
                'interest_rate' => 18.00, // 18% anual
                'min_term_months' => 6,
                'max_term_months' => 36,
                'requires_guarantee' => true,
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Microcrédito',
                'description' => 'Préstamos pequeños para emprendedores',
                'min_amount' => 100,
                'max_amount' => 1000,
                'interest_rate' => 36.00, // 36% anual
                'min_term_months' => 1,
                'max_term_months' => 12,
                'requires_guarantee' => false,
                'is_active' => true,
                'created_by' => 1
            ],
            [
                'name' => 'Crédito Hipotecario',
                'description' => 'Financiamiento para compra o mejora de vivienda',
                'min_amount' => 20000,
                'max_amount' => 200000,
                'interest_rate' => 12.00, // 12% anual
                'min_term_months' => 60,
                'max_term_months' => 240,
                'requires_guarantee' => true,
                'is_active' => true,
                'created_by' => 1
            ]
        ];

        foreach ($creditTypes as $type) {
            CreditType::create($type);
        }
    }
} 