<?php
$host = '127.0.0.1';
$db = 'sistema_prestamos';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "=== VERIFICANDO TABLA ROUTE ===\n\n";
    
    // Verificar estructura
    $stmt = $pdo->query("DESCRIBE route");
    $columns = [];
    echo "Estructura de la tabla 'route':\n";
    while ($row = $stmt->fetch()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
        $columns[] = $row['Field'];
    }
    
    echo "\nColumnas encontradas: " . count($columns) . "\n\n";
    
    // Verificar datos
    $stmt = $pdo->query("SELECT * FROM route");
    $rows = $stmt->fetchAll();
    
    echo "Registros en la tabla 'route': " . count($rows) . "\n\n";
    
    if (count($rows) > 0) {
        foreach ($rows as $i => $row) {
            echo "Registro " . ($i+1) . ":\n";
            foreach ($row as $key => $value) {
                echo "- $key: " . (is_null($value) ? "NULL" : $value) . "\n";
            }
            echo "\n";
        }
    } else {
        echo "No hay registros en la tabla 'route'.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 