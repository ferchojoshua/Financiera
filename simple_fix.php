<?php
// Script simple para verificar la estructura de la tabla credit

$host = '127.0.0.1';
$database = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión establecida correctamente.\n";
    
    // Verificar la estructura de la tabla credit
    $stmt = $pdo->query("DESCRIBE credit");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Columnas encontradas en la tabla credit:\n";
    
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    // Verificar si la consulta funciona
    $stmt = $pdo->query("
        SELECT sum(`amount_neto`) as total 
        FROM `credit` 
        WHERE `status` = 'active' 
        AND `disbursement_date` IS NOT NULL 
        AND DATE_ADD(disbursement_date, INTERVAL 30 DAY) < CURDATE()
    ");
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nConsulta con 'amount_neto' ejecutada correctamente.\n";
    echo "Total: " . ($result['total'] ?? 'NULL') . "\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 