<?php
/**
 * Script para añadir las columnas faltantes a la tabla credit
 * y crear la tabla routes si no existe
 */

echo "Iniciando corrección de base de datos...\n";

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
    
    // Verificar si las columnas existen en la tabla credit
    echo "Verificando columnas en la tabla credit...\n";
    $columns = [];
    $result = $pdo->query("SHOW COLUMNS FROM credit");
    foreach ($result as $row) {
        $columns[] = $row['Field'];
    }
    
    // Añadir columna disbursement_date si no existe
    if (!in_array('disbursement_date', $columns)) {
        echo "Añadiendo columna disbursement_date...\n";
        $pdo->exec("ALTER TABLE credit ADD COLUMN disbursement_date DATE NULL COMMENT 'Fecha de desembolso del préstamo'");
        echo "Columna disbursement_date añadida.\n";
    } else {
        echo "La columna disbursement_date ya existe.\n";
    }
    
    // Añadir columna cancellation_date si no existe
    if (!in_array('cancellation_date', $columns)) {
        echo "Añadiendo columna cancellation_date...\n";
        $pdo->exec("ALTER TABLE credit ADD COLUMN cancellation_date DATE NULL COMMENT 'Fecha de cancelación del préstamo'");
        echo "Columna cancellation_date añadida.\n";
    } else {
        echo "La columna cancellation_date ya existe.\n";
    }
    
    // Verificar si la tabla routes existe
    echo "Verificando si la tabla routes existe...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'routes'");
    if ($stmt->rowCount() == 0) {
        echo "Creando tabla routes...\n";
        $sql = "CREATE TABLE routes (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL COMMENT 'Nombre de la ruta',
            description TEXT NULL COMMENT 'Descripción de la ruta',
            collector_id BIGINT UNSIGNED NULL COMMENT 'ID del cobrador asignado',
            status VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT 'Estado: active, inactive',
            frequency VARCHAR(20) NULL COMMENT 'Frecuencia: daily, weekly, monthly',
            start_time TIME NULL COMMENT 'Hora de inicio',
            end_time TIME NULL COMMENT 'Hora de fin',
            client_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cantidad de clientes en la ruta',
            credit_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Cantidad de créditos en la ruta',
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id),
            INDEX idx_collector_id (collector_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);
        echo "Tabla routes creada con éxito.\n";
    } else {
        echo "La tabla routes ya existe.\n";
    }
    
    echo "Corrección completada con éxito.\n";
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage() . "\n";
} 