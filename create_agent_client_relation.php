<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=========================================\n";
    echo "Verificación de la tabla agent_has_client\n";
    echo "=========================================\n\n";
    
    // Verificar si existe la tabla agent_has_client
    $tableExists = $conn->query("SHOW TABLES LIKE 'agent_has_client'")->rowCount() > 0;
    
    if (!$tableExists) {
        echo "La tabla agent_has_client no existe. Creándola...\n";
        
        // Crear la tabla agent_has_client
        $sql = "CREATE TABLE IF NOT EXISTS `agent_has_client` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_agent` int UNSIGNED NOT NULL,
            `id_client` int UNSIGNED NOT NULL,
            `id_wallet` int UNSIGNED DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `agent_has_client_id_agent_foreign` (`id_agent`),
            KEY `agent_has_client_id_client_foreign` (`id_client`),
            KEY `agent_has_client_id_wallet_foreign` (`id_wallet`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        echo "Tabla agent_has_client creada correctamente.\n\n";
    } else {
        echo "La tabla agent_has_client ya existe.\n\n";
    }
    
    // Verificar si existe la tabla supervisor_has_agent
    $tableExists = $conn->query("SHOW TABLES LIKE 'agent_has_supervisor'")->rowCount() > 0;
    
    if (!$tableExists) {
        echo "La tabla agent_has_supervisor no existe. Creándola...\n";
        
        // Crear la tabla supervisor_has_agent
        $sql = "CREATE TABLE IF NOT EXISTS `agent_has_supervisor` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_supervisor` int UNSIGNED NOT NULL,
            `id_user_agent` int UNSIGNED NOT NULL,
            `id_wallet` int UNSIGNED DEFAULT NULL,
            `base` decimal(10,2) DEFAULT '0.00',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `agent_has_supervisor_id_supervisor_foreign` (`id_supervisor`),
            KEY `agent_has_supervisor_id_user_agent_foreign` (`id_user_agent`),
            KEY `agent_has_supervisor_id_wallet_foreign` (`id_wallet`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        echo "Tabla agent_has_supervisor creada correctamente.\n\n";
    } else {
        echo "La tabla agent_has_supervisor ya existe.\n\n";
    }
    
    // Buscar usuarios agentes sin relación con supervisor
    echo "Buscando usuarios agentes sin relación con supervisor...\n";
    $agentUsers = $conn->query("SELECT id, name, email FROM users WHERE role = 'agent' OR level = 'agent'")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($agentUsers) > 0) {
        echo "Se encontraron " . count($agentUsers) . " usuarios con rol de agente.\n";
        
        // Buscar supervisores
        $supervisors = $conn->query("SELECT id, name, email FROM users WHERE role = 'supervisor' OR level = 'supervisor' LIMIT 1")->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($supervisors) > 0) {
            $supervisor = $supervisors[0];
            echo "Se usará el supervisor: " . $supervisor['name'] . " (ID: " . $supervisor['id'] . ")\n";
            
            // Buscar carteras/billeteras
            $wallets = $conn->query("SELECT id, name FROM wallet LIMIT 1")->fetchAll(PDO::FETCH_ASSOC);
            $walletId = null;
            
            if (count($wallets) > 0) {
                $wallet = $wallets[0];
                $walletId = $wallet['id'];
                echo "Se usará la cartera: " . $wallet['name'] . " (ID: " . $walletId . ")\n";
            } else {
                echo "No se encontraron carteras. Creando una cartera predeterminada...\n";
                
                $sql = "INSERT INTO wallet (name, created_at, updated_at) VALUES ('Cartera Predeterminada', NOW(), NOW())";
                $conn->exec($sql);
                $walletId = $conn->lastInsertId();
                
                echo "Cartera predeterminada creada con ID: " . $walletId . "\n";
            }
            
            // Crear relaciones entre agentes y supervisor
            foreach ($agentUsers as $agent) {
                // Verificar si ya existe la relación
                $exists = $conn->query("SELECT id FROM agent_has_supervisor WHERE id_user_agent = " . $agent['id'])->rowCount() > 0;
                
                if (!$exists) {
                    $sql = "INSERT INTO agent_has_supervisor (id_supervisor, id_user_agent, id_wallet, base, created_at, updated_at) 
                            VALUES (" . $supervisor['id'] . ", " . $agent['id'] . ", " . $walletId . ", 5000.00, NOW(), NOW())";
                    $conn->exec($sql);
                    echo "Relación creada: Supervisor " . $supervisor['id'] . " - Agente " . $agent['id'] . " - Cartera " . $walletId . "\n";
                } else {
                    echo "La relación para el agente " . $agent['id'] . " ya existe.\n";
                }
            }
        } else {
            echo "No se encontraron supervisores. Creando uno predeterminado...\n";
            
            // Crear un supervisor predeterminado
            $sql = "INSERT INTO users (name, email, password, level, role, created_at, updated_at) 
                    VALUES ('Supervisor Predeterminado', 'supervisor@sistema.com', '" . password_hash('supervisor123', PASSWORD_DEFAULT) . "', 'supervisor', 'supervisor', NOW(), NOW())";
            $conn->exec($sql);
            $supervisorId = $conn->lastInsertId();
            
            echo "Supervisor predeterminado creado con ID: " . $supervisorId . "\n";
            
            // Crear cartera predeterminada si no existe
            $wallets = $conn->query("SELECT id, name FROM wallet LIMIT 1")->fetchAll(PDO::FETCH_ASSOC);
            $walletId = null;
            
            if (count($wallets) > 0) {
                $wallet = $wallets[0];
                $walletId = $wallet['id'];
            } else {
                $sql = "INSERT INTO wallet (name, created_at, updated_at) VALUES ('Cartera Predeterminada', NOW(), NOW())";
                $conn->exec($sql);
                $walletId = $conn->lastInsertId();
                echo "Cartera predeterminada creada con ID: " . $walletId . "\n";
            }
            
            // Crear relaciones entre agentes y supervisor nuevo
            foreach ($agentUsers as $agent) {
                $sql = "INSERT INTO agent_has_supervisor (id_supervisor, id_user_agent, id_wallet, base, created_at, updated_at) 
                        VALUES (" . $supervisorId . ", " . $agent['id'] . ", " . $walletId . ", 5000.00, NOW(), NOW())";
                $conn->exec($sql);
                echo "Relación creada: Supervisor " . $supervisorId . " - Agente " . $agent['id'] . " - Cartera " . $walletId . "\n";
            }
        }
    } else {
        echo "No se encontraron usuarios con rol de agente.\n";
    }
    
    echo "\n=========================================\n";
    echo "IMPORTANTE:\n";
    echo "=========================================\n\n";
    
    echo "1. Se han creado o verificado las tablas necesarias para la relación entre agentes y clientes.\n";
    echo "2. Se han creado las relaciones necesarias entre supervisores y agentes.\n";
    echo "3. Reinicia el servidor Laravel para aplicar los cambios:\n";
    echo "   - php artisan cache:clear\n";
    echo "   - php artisan config:clear\n";
    echo "   - php artisan route:clear\n";
    echo "   - Ctrl+C para detener el servidor actual y php artisan serve para reiniciarlo\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 