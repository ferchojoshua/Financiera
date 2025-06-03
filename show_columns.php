<?php
/**
 * Script para mostrar las columnas de la tabla credit
 */

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
    
    // Mostrar columnas de la tabla credit
    echo "Columnas de la tabla credit:\n";
    $result = $pdo->query("SHOW COLUMNS FROM credit");
    foreach ($result as $row) {
        echo "- " . $row['Field'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage() . "\n";
} 