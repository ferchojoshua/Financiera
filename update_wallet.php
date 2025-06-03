<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si la columna status ya existe en la tabla wallet
    $columnExists = false;
    $stmt = $conn->query("DESCRIBE wallet");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Field'] == 'status') {
            $columnExists = true;
            break;
        }
    }
    
    if (!$columnExists) {
        // Agregar la columna status a la tabla wallet
        $sql = "ALTER TABLE wallet ADD COLUMN status enum('activa', 'inactiva', 'bloqueada') DEFAULT 'activa'";
        $conn->exec($sql);
        echo "Columna 'status' agregada a la tabla wallet.";
    } else {
        echo "La columna 'status' ya existe en la tabla wallet.";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 