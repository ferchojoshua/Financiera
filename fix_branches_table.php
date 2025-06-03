<?php
/**
 * Script para crear la tabla branches si no existe
 */

echo "Iniciando creación de tabla branches...\n";

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
    
    // Verificar si la tabla branches existe
    echo "Verificando si la tabla branches existe...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'branches'");
    if ($stmt->rowCount() == 0) {
        echo "Creando tabla branches...\n";
        $sql = "CREATE TABLE branches (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(191) NOT NULL,
            address VARCHAR(255) NULL,
            phone VARCHAR(50) NULL,
            email VARCHAR(100) NULL,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);
        
        // Insertar sucursal predeterminada
        echo "Insertando sucursal predeterminada...\n";
        $sql = "INSERT INTO branches (name, address, phone, email, status, created_at, updated_at) 
                VALUES ('Sucursal Principal', 'Dirección Principal', '12345678', 'sucursal@example.com', 'active', NOW(), NOW());";
        
        $pdo->exec($sql);
        
        echo "Tabla branches creada con éxito y se ha agregado una sucursal predeterminada.\n";
    } else {
        // Verificar si la tabla tiene la columna status
        $stmt = $pdo->query("SHOW COLUMNS FROM branches LIKE 'status'");
        if ($stmt->rowCount() == 0) {
            echo "Agregando columna status a la tabla branches...\n";
            $sql = "ALTER TABLE branches ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER email";
            $pdo->exec($sql);
        }
        
        // Verificar si hay alguna sucursal
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM branches");
        $row = $stmt->fetch();
        if ($row['count'] == 0) {
            echo "Insertando sucursal predeterminada...\n";
            $sql = "INSERT INTO branches (name, address, phone, email, status, created_at, updated_at) 
                    VALUES ('Sucursal Principal', 'Dirección Principal', '12345678', 'sucursal@example.com', 'active', NOW(), NOW());";
            
            $pdo->exec($sql);
        }
        
        echo "La tabla branches ya existe. Se han verificado las columnas y datos necesarios.\n";
    }
    
    echo "Corrección completada con éxito.\n";
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage() . "\n";
} 