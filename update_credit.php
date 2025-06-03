<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener la estructura actual de la tabla credit
    $stmt = $conn->query("DESCRIBE credit");
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
    
    echo "Columnas actuales en la tabla credit:\n";
    foreach ($columns as $column) {
        echo "- " . $column . "\n";
    }
    
    // Lista de columnas que debemos verificar y agregar si no existen
    $requiredColumns = [
        'cancellation_date' => "ADD COLUMN cancellation_date DATETIME NULL",
        'disbursement_date' => "ADD COLUMN disbursement_date DATETIME NULL",
        'is_overdue' => "ADD COLUMN is_overdue TINYINT(1) DEFAULT 0",
        'amount' => "ADD COLUMN amount DECIMAL(15,2) DEFAULT 0",
        'interest_rate' => "ADD COLUMN interest_rate DECIMAL(5,2) DEFAULT 0",
        'remaining_payments' => "ADD COLUMN remaining_payments INT DEFAULT 0"
    ];
    
    foreach ($requiredColumns as $column => $addSql) {
        if (!in_array($column, $columns)) {
            $sql = "ALTER TABLE credit " . $addSql;
            $conn->exec($sql);
            echo "Columna '$column' agregada a la tabla credit.\n";
        } else {
            echo "La columna '$column' ya existe en la tabla credit.\n";
        }
    }
    
    // Actualizar todos los registros para asignar valores predeterminados
    $sql = "UPDATE credit SET 
            amount = 1000 WHERE amount = 0 OR amount IS NULL,
            interest_rate = 10 WHERE interest_rate = 0 OR interest_rate IS NULL,
            remaining_payments = 5 WHERE remaining_payments = 0 OR remaining_payments IS NULL,
            is_overdue = 0 WHERE is_overdue IS NULL";
    $affected = $conn->exec($sql);
    
    echo "\nSe actualizaron $affected registros en la tabla credit con valores predeterminados.";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 