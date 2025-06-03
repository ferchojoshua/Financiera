<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_prestamos', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare('INSERT INTO reports (name, description, report_type, output_format, created_by, is_public, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    
    $stmt->execute([
        'Reporte de Prueba',
        'Descripción del reporte de prueba',
        'financial',
        'pdf',
        1,
        1,
        'active'
    ]);
    
    echo "Registro insertado correctamente\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 