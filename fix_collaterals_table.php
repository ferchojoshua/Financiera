<?php
/**
 * Script para crear la tabla collaterals si no existe
 */

echo "Iniciando creación de tabla collaterals...\n";

try {
    // Conexión a la base de datos
    $host = '127.0.0.1';
    $db   = 'sistema_prestamos';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    echo "Conectando a la base de datos...\n";
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Conexión exitosa.\n";
    
    // Verificar si la tabla collaterals existe
    echo "Verificando si la tabla collaterals existe...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'collaterals'");
    if ($stmt->rowCount() == 0) {
        echo "Creando tabla collaterals...\n";
        $sql = "CREATE TABLE collaterals (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT UNSIGNED NOT NULL,
            credit_id BIGINT UNSIGNED NULL,
            type VARCHAR(50) NOT NULL COMMENT 'Tipo de garantía: inmueble, vehículo, etc',
            description TEXT NOT NULL COMMENT 'Descripción detallada de la garantía',
            value DECIMAL(15, 2) NOT NULL COMMENT 'Valor estimado de la garantía',
            status VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT 'Estado: active, verified, rejected',
            document_path VARCHAR(255) NULL COMMENT 'Ruta al documento de respaldo',
            verification_date DATETIME NULL COMMENT 'Fecha de verificación',
            verified_by INT UNSIGNED NULL COMMENT 'Usuario que verificó la garantía',
            notes TEXT NULL COMMENT 'Notas adicionales',
            is_pyme TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si la garantía es para un préstamo PYME',
            client_type ENUM('natural', 'juridica') NOT NULL DEFAULT 'natural' COMMENT 'Tipo de cliente: persona natural o jurídica',
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX idx_user_id (user_id),
            INDEX idx_credit_id (credit_id),
            INDEX idx_verified_by (verified_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);
        echo "Tabla collaterals creada con éxito.\n";
    } else {
        echo "La tabla collaterals ya existe.\n";
        
        // Verificar si la tabla tiene todas las columnas necesarias
        $columns = [
            'user_id' => 'INT UNSIGNED NOT NULL',
            'credit_id' => 'BIGINT UNSIGNED NULL',
            'type' => 'VARCHAR(50) NOT NULL',
            'description' => 'TEXT NOT NULL',
            'value' => 'DECIMAL(15,2) NOT NULL',
            'status' => 'VARCHAR(20) NOT NULL DEFAULT "active"',
            'document_path' => 'VARCHAR(255) NULL',
            'verification_date' => 'DATETIME NULL',
            'verified_by' => 'INT UNSIGNED NULL',
            'notes' => 'TEXT NULL',
            'is_pyme' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'client_type' => 'ENUM("natural", "juridica") NOT NULL DEFAULT "natural"'
        ];
        
        echo "Verificando si la tabla tiene todas las columnas necesarias...\n";
        foreach ($columns as $column => $definition) {
            $stmt = $pdo->query("SHOW COLUMNS FROM collaterals LIKE '$column'");
            if ($stmt->rowCount() == 0) {
                echo "Agregando columna $column a la tabla collaterals...\n";
                $sql = "ALTER TABLE collaterals ADD COLUMN $column $definition";
                $pdo->exec($sql);
                echo "Columna $column agregada.\n";
            }
        }
        
        // Verificar si la tabla tiene los índices necesarios
        $indices = [
            'idx_user_id' => 'user_id',
            'idx_credit_id' => 'credit_id',
            'idx_verified_by' => 'verified_by'
        ];
        
        echo "Verificando si la tabla tiene todos los índices necesarios...\n";
        foreach ($indices as $index => $column) {
            $stmt = $pdo->query("SHOW INDEX FROM collaterals WHERE Key_name = '$index'");
            if ($stmt->rowCount() == 0) {
                echo "Agregando índice $index a la tabla collaterals...\n";
                $sql = "ALTER TABLE collaterals ADD INDEX $index ($column)";
                $pdo->exec($sql);
                echo "Índice $index agregado.\n";
            }
        }
    }
    
    echo "Corrección completada con éxito.\n";
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage() . "\n";
} 