<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consultar la estructura de la tabla users
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Estructura de la tabla 'users':\n";
    foreach ($columns as $column) {
        echo "- " . $column . "\n";
    }
    
    // Verificar si el usuario admin existe
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@prestamos.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "\nInformación del usuario admin@prestamos.com:\n";
        foreach ($user as $key => $value) {
            echo "- " . $key . ": " . $value . "\n";
        }
        
        // Actualizar el usuario si es necesario
        $password = password_hash('12345678', PASSWORD_BCRYPT);
        $now = date('Y-m-d H:i:s');
        
        // Verificamos si tiene el rol correcto
        if (isset($user['role']) && $user['role'] !== 'superadmin') {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE email = ?");
            $stmt->execute(['superadmin', 'admin@prestamos.com']);
            echo "\nSe ha actualizado el rol a 'superadmin'";
        }
        
        // Actualizar contraseña por si acaso
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$password, 'admin@prestamos.com']);
        echo "\nSe ha actualizado la contraseña a '12345678'";
    } else {
        echo "\nEl usuario admin@prestamos.com no existe en la base de datos.";
    }
    
    // Mostrar todos los usuarios
    $stmt = $conn->query("SELECT id, name, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n\nUsuarios en la base de datos:\n";
    foreach ($users as $user) {
        echo "- ID: " . $user['id'] . ", Nombre: " . $user['name'] . ", Email: " . $user['email'] . ", Rol: " . ($user['role'] ?? 'N/A') . "\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 