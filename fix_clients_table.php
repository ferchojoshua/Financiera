<?php

// Iniciar el script
echo "Verificando la tabla 'clients'...\n";

try {
    // Conexión a la base de datos
    $dsn = 'mysql:host=127.0.0.1;dbname=sistema_prestamos;charset=utf8mb4';
    $username = 'root';
    $password = '';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Verificar si la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'clients'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "La tabla 'clients' ya existe.\n";
    } else {
        echo "La tabla 'clients' no existe. Creándola...\n";
        
        // Crear la tabla
        $pdo->exec("CREATE TABLE `clients` (
            `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `nit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `dui` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address` text COLLATE utf8mb4_unicode_ci,
            `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `birthdate` date DEFAULT NULL,
            `business_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `tax_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `business_sector` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `economic_activity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `annual_revenue` decimal(15,2) DEFAULT NULL,
            `employee_count` int(11) DEFAULT NULL,
            `founding_date` date DEFAULT NULL,
            `legal_representative` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `risk_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `credit_notes` text COLLATE utf8mb4_unicode_ci,
            `blacklisted` tinyint(1) DEFAULT '0',
            `blacklist_reason` text COLLATE utf8mb4_unicode_ci,
            `assigned_agent_id` int(10) UNSIGNED DEFAULT NULL,
            `credit_score` int(11) DEFAULT NULL,
            `is_active` tinyint(1) DEFAULT '1',
            `created_by` int(10) UNSIGNED DEFAULT NULL,
            `updated_by` int(10) UNSIGNED DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `clients_assigned_agent_id_index` (`assigned_agent_id`),
            KEY `clients_is_active_index` (`is_active`),
            KEY `clients_blacklisted_index` (`blacklisted`),
            KEY `clients_created_by_index` (`created_by`),
            KEY `clients_updated_by_index` (`updated_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        
        echo "Tabla 'clients' creada exitosamente.\n";
    }
    
    // Verificar si existe la tabla client_records para los expedientes
    $stmt = $pdo->query("SHOW TABLES LIKE 'client_records'");
    $recordsTableExists = $stmt->rowCount() > 0;
    
    if ($recordsTableExists) {
        echo "La tabla 'client_records' ya existe.\n";
    } else {
        echo "La tabla 'client_records' no existe. Creándola...\n";
        
        // Crear la tabla
        $pdo->exec("CREATE TABLE `client_records` (
            `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `client_id` int(10) UNSIGNED NOT NULL,
            `record_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `record_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `created_by` int(10) UNSIGNED DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `client_records_client_id_index` (`client_id`),
            KEY `client_records_record_type_index` (`record_type`),
            KEY `client_records_record_date_index` (`record_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        
        echo "Tabla 'client_records' creada exitosamente.\n";
    }
    
    echo "Proceso completado exitosamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
} 