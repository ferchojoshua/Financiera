<?php
require 'vendor/autoload.php';

try {
    // Carga el archivo de entorno para obtener la configuración de base de datos
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Conexión a la base de datos usando variables de entorno
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $database = $_ENV['DB_DATABASE'] ?? 'sistema_prestamos';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CORRECCIÓN DE LA TABLA ROUTE ===\n\n";
    
    // Verificar si la tabla route existe
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'route'");
    $stmt->execute();
    $routeTableExists = $stmt->rowCount() > 0;
    
    if ($routeTableExists) {
        echo "La tabla 'route' existe. Verificando columnas...\n";
        
        // Obtener las columnas actuales
        $stmt = $pdo->query("DESCRIBE `route`");
        $existingColumns = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $existingColumns[] = $row['Field'];
        }
        
        echo "Columnas existentes: " . implode(", ", $existingColumns) . "\n";
        
        // Columnas que debería tener la tabla
        $requiredColumns = [
            'id', 'name', 'description', 'collector_id', 'supervisor_id', 
            'status', 'zone', 'days', 'created_by', 'updated_by', 
            'created_at', 'updated_at'
        ];
        
        // Agregar columnas que faltan
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $existingColumns)) {
                echo "Agregando columna '$column'...\n";
                
                switch ($column) {
                    case 'name':
                        $pdo->exec("ALTER TABLE `route` ADD COLUMN `$column` VARCHAR(100) NOT NULL");
                        break;
                    case 'description':
                        $pdo->exec("ALTER TABLE `route` ADD COLUMN `$column` TEXT NULL");
                        break;
                    case 'collector_id':
                    case 'supervisor_id':
                    case 'created_by':
                    case 'updated_by':
                        $pdo->exec("ALTER TABLE `route` ADD COLUMN `$column` INT UNSIGNED NULL");
                        break;
                    case 'status':
                        $pdo->exec("ALTER TABLE `route` ADD COLUMN `$column` ENUM('active', 'inactive') NOT NULL DEFAULT 'active'");
                        break;
                    case 'zone':
                        $pdo->exec("ALTER TABLE `route` ADD COLUMN `$column` VARCHAR(100) NULL");
                        break;
                    case 'days':
                        $pdo->exec("ALTER TABLE `route` ADD COLUMN `$column` JSON NULL");
                        break;
                    case 'created_at':
                    case 'updated_at':
                        if (!in_array($column, $existingColumns)) {
                            $pdo->exec("ALTER TABLE `route` ADD COLUMN `$column` TIMESTAMP NULL");
                        }
                        break;
                }
            }
        }
        
        // Crear una ruta de ejemplo si no hay ninguna
        $stmt = $pdo->query("SELECT COUNT(*) FROM `route`");
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            echo "No hay rutas. Creando una ruta de ejemplo...\n";
            
            // Buscar un cobrador
            $stmt = $pdo->query("SELECT id FROM `users` WHERE `role` = 'colector' OR `role` = 'cobrador' LIMIT 1");
            $collector = $stmt->fetch(PDO::FETCH_ASSOC);
            $collectorId = $collector ? $collector['id'] : null;
            
            // Buscar un supervisor
            $stmt = $pdo->query("SELECT id FROM `users` WHERE `role` = 'supervisor' LIMIT 1");
            $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
            $supervisorId = $supervisor ? $supervisor['id'] : null;
            
            // Crear ruta de ejemplo
            $stmt = $pdo->prepare("
                INSERT INTO `route` (
                    `name`, `description`, `collector_id`, `supervisor_id`, 
                    `status`, `zone`, `days`, `created_by`, `created_at`, `updated_at`
                ) VALUES (
                    'Ruta Principal', 'Ruta de ejemplo creada automáticamente', ?, ?, 
                    'active', 'Zona Centro', ?, 1, NOW(), NOW()
                )
            ");
            
            $days = json_encode(["monday", "wednesday", "friday"]);
            $stmt->execute([$collectorId, $supervisorId, $days]);
            
            echo "Ruta de ejemplo creada con éxito.\n";
        }
        
        echo "\nLa tabla 'route' ha sido corregida correctamente.\n";
    } else {
        echo "La tabla 'route' no existe. Creando la tabla...\n";
        
        // Crear la tabla desde cero
        $pdo->exec("
            CREATE TABLE `route` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                `description` TEXT NULL,
                `collector_id` INT UNSIGNED NULL,
                `supervisor_id` INT UNSIGNED NULL,
                `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
                `zone` VARCHAR(100) NULL,
                `days` JSON NULL,
                `created_by` INT UNSIGNED NULL,
                `updated_by` INT UNSIGNED NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`id`)
            )
        ");
        
        echo "Tabla 'route' creada con éxito.\n";
        
        // Crear una ruta de ejemplo
        echo "Creando una ruta de ejemplo...\n";
        
        // Buscar un cobrador
        $stmt = $pdo->query("SELECT id FROM `users` WHERE `role` = 'colector' OR `role` = 'cobrador' LIMIT 1");
        $collector = $stmt->fetch(PDO::FETCH_ASSOC);
        $collectorId = $collector ? $collector['id'] : null;
        
        // Buscar un supervisor
        $stmt = $pdo->query("SELECT id FROM `users` WHERE `role` = 'supervisor' LIMIT 1");
        $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
        $supervisorId = $supervisor ? $supervisor['id'] : null;
        
        // Crear ruta de ejemplo
        $stmt = $pdo->prepare("
            INSERT INTO `route` (
                `name`, `description`, `collector_id`, `supervisor_id`, 
                `status`, `zone`, `days`, `created_by`, `created_at`, `updated_at`
            ) VALUES (
                'Ruta Principal', 'Ruta de ejemplo creada automáticamente', ?, ?, 
                'active', 'Zona Centro', ?, 1, NOW(), NOW()
            )
        ");
        
        $days = json_encode(["monday", "wednesday", "friday"]);
        $stmt->execute([$collectorId, $supervisorId, $days]);
        
        echo "Ruta de ejemplo creada con éxito.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "En el archivo: " . $e->getFile() . " línea " . $e->getLine() . "\n";
} 