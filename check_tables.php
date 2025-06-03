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
    
    echo "=== VERIFICACIÓN DE TABLAS ===\n\n";
    
    // Tablas a verificar
    $tables = ['route', 'branches', 'users'];
    
    foreach ($tables as $table) {
        // Verificar si la tabla existe
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->rowCount() > 0;
        
        echo "Tabla '$table': " . ($exists ? "EXISTE" : "NO EXISTE") . "\n";
        
        if ($exists) {
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
            $count = $stmt->fetchColumn();
            echo "  - Cantidad de registros: $count\n";
            
            // Mostrar estructura
            $stmt = $pdo->query("DESCRIBE `$table`");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "  - Columnas:\n";
            foreach ($columns as $column) {
                echo "    * {$column['Field']} ({$column['Type']})\n";
            }
            
            // Mostrar algunos registros
            if ($count > 0) {
                $stmt = $pdo->query("SELECT * FROM `$table` LIMIT 5");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "  - Primeros registros:\n";
                foreach ($rows as $i => $row) {
                    echo "    * Registro " . ($i + 1) . ":\n";
                    foreach ($row as $key => $value) {
                        echo "      - $key: " . (is_null($value) ? "NULL" : $value) . "\n";
                    }
                }
            }
        }
        
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} 