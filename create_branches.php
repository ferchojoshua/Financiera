<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Comprobar si la tabla branches ya existe
    $tableExists = $conn->query("SHOW TABLES LIKE 'branches'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Crear la tabla branches
        $sql = "CREATE TABLE IF NOT EXISTS `branches` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `address` varchar(255) NULL,
            `phone` varchar(50) NULL,
            `email` varchar(255) NULL
            `manager_id` int UNSIGNED NULL,
            `status` enum('active','inactive') NOT NULL DEFAULT 'active',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        
        // Insertar una sucursal por defecto
        $sql = "INSERT INTO branches (name, address, status, created_at, updated_at) 
                VALUES ('Sucursal Principal', 'Dirección Principal', 'active', NOW(), NOW())";
        $conn->exec($sql);
        
        echo "Tabla branches creada correctamente con una sucursal predeterminada.";
    } else {
        echo "La tabla branches ya existe.";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 