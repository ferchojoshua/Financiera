<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Comprobar si la tabla wallets ya existe
    $tableExists = $conn->query("SHOW TABLES LIKE 'wallets'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Crear la tabla wallets
        $sql = "CREATE TABLE IF NOT EXISTS `wallets` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `balance` decimal(15,2) DEFAULT 0.00,
            `description` text DEFAULT NULL,
            `created_by` bigint(20) UNSIGNED DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `wallets_user_id_index` (`user_id`),
            KEY `wallets_created_by_index` (`created_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        echo "Tabla wallets creada correctamente.";
    } else {
        echo "La tabla wallets ya existe.";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 