<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si la tabla users existe
    $tableExists = $conn->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    
    if ($tableExists) {
        // Verificar si el usuario ya existe
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['admin@prestamos.com']);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "El usuario admin@prestamos.com ya existe en la base de datos.";
        } else {
            // Crear el usuario administrador
            $password = password_hash('12345678', PASSWORD_BCRYPT);
            $now = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO users (name, email, password, role, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['Admin', 'admin@prestamos.com', $password, 'superadmin', $now, $now]);
            
            echo "Usuario administrador creado correctamente con email: admin@prestamos.com y contraseña: 12345678";
        }
    } else {
        echo "La tabla 'users' no existe en la base de datos.";
        
        // Mostrar todas las tablas disponibles
        $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "\nTablas disponibles en la base de datos:";
        foreach ($tables as $table) {
            echo "\n- " . $table;
        }
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 