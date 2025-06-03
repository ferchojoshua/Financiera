<?php
/**
 * Script para corregir problemas en la base de datos del sistema de préstamos
 * 
 * Este script ejecuta las correcciones SQL directamente sin usar migraciones
 */

echo "Iniciando corrección de problemas en la base de datos...\n";

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
    
    // Crear tabla de migraciones si no existe
    echo "Creando tabla de migraciones si no existe...\n";
    $sql = "CREATE TABLE IF NOT EXISTS migrations (
        id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        migration VARCHAR(191) NOT NULL,
        batch INT(11) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($sql);
    
    // Verificar si la tabla wallet existe
    echo "Verificando si la tabla wallet existe...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'wallet'");
    if ($stmt->rowCount() == 0) {
        // Crear tabla wallet si no existe
        echo "Creando tabla wallet...\n";
        $sql = "CREATE TABLE wallet (
            id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(191) NOT NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);
        
        // Insertar cartera predeterminada
        echo "Insertando cartera predeterminada...\n";
        $sql = "INSERT INTO wallet (name, created_at, updated_at) 
                VALUES ('Cartera Predeterminada', NOW(), NOW());";
        
        $pdo->exec($sql);
    } else {
        // Verificar si las columnas created_at y updated_at existen
        $stmt = $pdo->query("SHOW COLUMNS FROM wallet LIKE 'created_at'");
        if ($stmt->rowCount() == 0) {
            echo "Agregando columna created_at a la tabla wallet...\n";
            $sql = "ALTER TABLE wallet ADD COLUMN created_at TIMESTAMP NULL DEFAULT NULL";
            $pdo->exec($sql);
        }
        
        $stmt = $pdo->query("SHOW COLUMNS FROM wallet LIKE 'updated_at'");
        if ($stmt->rowCount() == 0) {
            echo "Agregando columna updated_at a la tabla wallet...\n";
            $sql = "ALTER TABLE wallet ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL";
            $pdo->exec($sql);
        }
        
        // Verificar si hay alguna cartera
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM wallet");
        $row = $stmt->fetch();
        if ($row['count'] == 0) {
            echo "Insertando cartera predeterminada...\n";
            $sql = "INSERT INTO wallet (name, created_at, updated_at) 
                    VALUES ('Cartera Predeterminada', NOW(), NOW());";
            
            $pdo->exec($sql);
        }
    }
    
    // Crear tabla agent_has_supervisor si no existe
    echo "Verificando si la tabla agent_has_supervisor existe...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'agent_has_supervisor'");
    if ($stmt->rowCount() == 0) {
        echo "Creando tabla agent_has_supervisor...\n";
        $sql = "CREATE TABLE agent_has_supervisor (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            id_user_agent BIGINT(20) UNSIGNED NOT NULL,
            id_supervisor BIGINT(20) UNSIGNED NOT NULL,
            base FLOAT DEFAULT 0,
            id_wallet INT(10) UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);
    } else {
        // Verificar si las columnas necesarias existen
        $columns = ['id_user_agent', 'id_supervisor', 'base', 'id_wallet', 'created_at', 'updated_at'];
        foreach ($columns as $column) {
            $stmt = $pdo->query("SHOW COLUMNS FROM agent_has_supervisor LIKE '$column'");
            if ($stmt->rowCount() == 0) {
                echo "Agregando columna $column a la tabla agent_has_supervisor...\n";
                
                $type = "BIGINT(20) UNSIGNED NOT NULL";
                if ($column == 'base') {
                    $type = "FLOAT DEFAULT 0";
                } elseif ($column == 'id_wallet') {
                    $type = "INT(10) UNSIGNED NOT NULL";
                } elseif ($column == 'created_at' || $column == 'updated_at') {
                    $type = "TIMESTAMP NULL DEFAULT NULL";
                }
                
                $sql = "ALTER TABLE agent_has_supervisor ADD COLUMN $column $type";
                $pdo->exec($sql);
            }
        }
    }
    
    // Verificar si la columna role existe en la tabla users
    echo "Verificando si la tabla users existe...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
        if ($stmt->rowCount() == 0) {
            echo "Agregando columna 'role' a la tabla 'users'...\n";
            $sql = "ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'user' AFTER email";
            $pdo->exec($sql);
        } else {
            echo "La columna 'role' ya existe en la tabla 'users'.\n";
        }
        
        // Verificar si la columna level existe
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'level'");
        if ($stmt->rowCount() > 0) {
            // Actualizar columna role basándose en level
            echo "Actualizando valores de role basados en level...\n";
            $sql = "UPDATE users SET role = level WHERE role IS NULL AND level IS NOT NULL";
            $pdo->exec($sql);
        }
        
        // Para usuarios que no tengan role, asignar 'user'
        $sql = "UPDATE users SET role = 'user' WHERE role IS NULL";
        $pdo->exec($sql);
    } else {
        echo "La tabla 'users' no existe. Asegúrate de que la base de datos está correctamente configurada.\n";
    }
    
    echo "¡Correcciones de base de datos aplicadas con éxito!\n";
    echo "Se han realizado las siguientes correcciones:\n";
    echo "1. Se ha creado o verificado la tabla 'migrations'\n";
    echo "2. Se ha creado o verificado la tabla 'wallet'\n";
    echo "3. Se ha creado o verificado la tabla 'agent_has_supervisor'\n";
    echo "4. Se ha asegurado que la columna 'role' existe en la tabla 'users'\n";
    echo "5. Se han actualizado los valores de 'role' basados en 'level'\n";
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage() . "\n";
} 