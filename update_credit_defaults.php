<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Actualizar registros individualmente para evitar errores de sintaxis
    $updates = [
        "UPDATE credit SET amount = 1000 WHERE amount = 0 OR amount IS NULL",
        "UPDATE credit SET interest_rate = 10 WHERE interest_rate = 0 OR interest_rate IS NULL",
        "UPDATE credit SET remaining_payments = 5 WHERE remaining_payments = 0 OR remaining_payments IS NULL",
        "UPDATE credit SET is_overdue = 0 WHERE is_overdue IS NULL",
        "UPDATE credit SET disbursement_date = created_at WHERE disbursement_date IS NULL",
        "UPDATE credit SET cancellation_date = DATE_ADD(created_at, INTERVAL 30 DAY) WHERE status = 'close' AND cancellation_date IS NULL"
    ];
    
    $totalAffected = 0;
    foreach ($updates as $sql) {
        $affected = $conn->exec($sql);
        $totalAffected += $affected;
        echo "Ejecutada consulta: $sql\n";
        echo "Registros afectados: $affected\n\n";
    }
    
    echo "Total de registros actualizados: $totalAffected";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 