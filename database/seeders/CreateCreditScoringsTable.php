<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateCreditScoringsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Eliminar la tabla si existe
        DB::statement('DROP TABLE IF EXISTS `credit_scorings`');

        // Crear la tabla
        DB::statement('
            CREATE TABLE IF NOT EXISTS `credit_scorings` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `loan_application_id` bigint(20) UNSIGNED NOT NULL,
                `user_id` bigint(20) UNSIGNED NOT NULL,
                `score` decimal(10,2) NOT NULL,
                `risk_level` enum("very_low","low","medium","high","very_high") NOT NULL,
                `scoring_model` varchar(255) NOT NULL,
                `financial_indicators` json NOT NULL,
                `qualitative_factors` json DEFAULT NULL,
                `external_bureau_data` json DEFAULT NULL,
                `calculated_by` bigint(20) UNSIGNED DEFAULT NULL,
                `calculation_date` datetime DEFAULT NULL,
                `recommendation` enum("approve","reject","review") NOT NULL,
                `notes` text DEFAULT NULL,
                `analyst_id` bigint(20) UNSIGNED DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `credit_scorings_loan_application_id_foreign` (`loan_application_id`),
                KEY `credit_scorings_user_id_foreign` (`user_id`),
                KEY `credit_scorings_calculated_by_foreign` (`calculated_by`),
                KEY `credit_scorings_analyst_id_foreign` (`analyst_id`),
                CONSTRAINT `credit_scorings_loan_application_id_foreign` FOREIGN KEY (`loan_application_id`) REFERENCES `loan_applications` (`id`) ON DELETE CASCADE,
                CONSTRAINT `credit_scorings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                CONSTRAINT `credit_scorings_calculated_by_foreign` FOREIGN KEY (`calculated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
                CONSTRAINT `credit_scorings_analyst_id_foreign` FOREIGN KEY (`analyst_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }
} 