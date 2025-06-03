<?php
/**
 * Script para agregar la columna business_name a la tabla users si no existe
 */

echo "Iniciando corrección de la columna business_name en la tabla users...\n";

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
    
    // Verificar si la columna business_name existe en la tabla users
    echo "Verificando si la columna business_name existe en la tabla users...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'business_name'");
    if ($stmt->rowCount() == 0) {
        echo "La columna business_name no existe en la tabla users. Agregándola...\n";
        
        // Verificar si existe la columna level para colocar business_name después de ella
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'level'");
        if ($stmt->rowCount() > 0) {
            $sql = "ALTER TABLE users ADD COLUMN business_name VARCHAR(255) NULL AFTER level";
        } else {
            $sql = "ALTER TABLE users ADD COLUMN business_name VARCHAR(255) NULL";
        }
        
        $pdo->exec($sql);
        echo "Columna business_name agregada correctamente.\n";
        
        // Agregar campos de empresa adicionales
        $columnsToAdd = [
            'business_sector' => 'VARCHAR(255) NULL',
            'economic_activity' => 'VARCHAR(255) NULL',
            'tax_id' => 'VARCHAR(50) NULL',
            'annual_revenue' => 'DECIMAL(15,2) NULL',
            'employee_count' => 'INT NULL',
            'founding_date' => 'DATE NULL',
            'legal_representative' => 'VARCHAR(255) NULL'
        ];
        
        echo "Agregando campos de empresa adicionales...\n";
        foreach ($columnsToAdd as $column => $definition) {
            $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE '$column'");
            if ($stmt->rowCount() == 0) {
                $sql = "ALTER TABLE users ADD COLUMN $column $definition";
                $pdo->exec($sql);
                echo "Columna $column agregada.\n";
            } else {
                echo "La columna $column ya existe.\n";
            }
        }
    } else {
        echo "La columna business_name ya existe en la tabla users.\n";
    }
    
    // Verificar si hay datos en las consultas que están fallando
    echo "Verificando consultas que podrían estar fallando...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE level = 'user' AND business_name IS NOT NULL");
    $row = $stmt->fetch();
    echo "Hay {$row['count']} usuarios con level='user' y business_name NOT NULL.\n";
    
    // Añadir un índice a business_name para mejorar el rendimiento
    echo "Verificando si existe un índice en business_name...\n";
    $stmt = $pdo->query("SHOW INDEX FROM users WHERE Column_name = 'business_name'");
    if ($stmt->rowCount() == 0) {
        echo "Creando índice en business_name...\n";
        $sql = "ALTER TABLE users ADD INDEX idx_business_name (business_name)";
        $pdo->exec($sql);
        echo "Índice creado correctamente.\n";
    } else {
        echo "Ya existe un índice en business_name.\n";
    }
    
    echo "Corrección completada con éxito.\n";
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage() . "\n";
} 