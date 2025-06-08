<?php
/**
 * Script para migrar usuarios de 'level' a 'role'
 * y asegurar que todos los usuarios tengan un rol válido
 */

// Configuración de la base de datos
$host = '127.0.0.1';
$db = 'sistema_prestamos';
$user = 'root';
$password = '';
$charset = 'utf8mb4';

try {
    // Conectar a la base de datos
    echo "Conectando a la base de datos...\n";
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $password, $options);
    echo "Conexión exitosa.\n";
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Verificar si existe la tabla user_roles
    $tableExists = $pdo->query("SHOW TABLES LIKE 'user_roles'")->rowCount() > 0;
    if (!$tableExists) {
        echo "Creando tabla user_roles...\n";
        $pdo->exec("
            CREATE TABLE user_roles (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                role_id BIGINT UNSIGNED NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
                UNIQUE(user_id, role_id)
            )
        ");
        echo "Tabla user_roles creada.\n";
    } else {
        echo "La tabla user_roles ya existe.\n";
    }
    
    // Obtener todos los usuarios
    echo "Obteniendo todos los usuarios...\n";
    $users = $pdo->query("SELECT id, level, role FROM users")->fetchAll();
    echo "Total de usuarios encontrados: " . count($users) . "\n";
    
    // Obtener todos los roles
    $roles = [];
    $result = $pdo->query("SELECT id, slug FROM roles");
    while ($row = $result->fetch()) {
        $roles[$row['slug']] = $row['id'];
    }
    echo "Roles disponibles: " . implode(", ", array_keys($roles)) . "\n";
    
    // Preparar consultas
    $updateUserRole = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $insertUserRole = $pdo->prepare("INSERT IGNORE INTO user_roles (user_id, role_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    
    // Para cada usuario, asignar el rol correcto
    $updated = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($users as $user) {
        $userId = $user['id'];
        $level = $user['level'];
        $role = $user['role'];
        
        // Determinar qué rol usar
        $roleSlug = null;
        
        // Si ya tiene un rol válido, usarlo
        if (!empty($role) && isset($roles[$role])) {
            $roleSlug = $role;
        } 
        // Si no, usar el nivel si es válido
        else if (!empty($level) && isset($roles[$level])) {
            $roleSlug = $level;
        }
        // Si level es un número, buscar el rol correspondiente por ID
        else if (is_numeric($level)) {
            foreach ($roles as $slug => $id) {
                if ($id == $level) {
                    $roleSlug = $slug;
                    break;
                }
            }
        }
        
        // Si sigue sin tener un rol válido, asignar rol por defecto
        if (!$roleSlug) {
            // Si level es 'client', asignar 'user'
            if ($level === 'client') {
                $roleSlug = 'user';
            }
            // En cualquier otro caso, asignar 'user' como valor predeterminado
            else {
                $roleSlug = 'user';
                echo "Usuario ID {$userId} con level='{$level}' y role='{$role}' no tiene un rol válido, asignando 'user' por defecto.\n";
            }
        }
        
        // Actualizar el campo 'role' en la tabla users
        try {
            $updateUserRole->execute([$roleSlug, $userId]);
            
            // Insertar en la tabla user_roles
            $roleId = $roles[$roleSlug];
            $insertUserRole->execute([$userId, $roleId]);
            
            $updated++;
        } catch (Exception $e) {
            echo "Error al actualizar usuario ID {$userId}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    
    // Confirmar la transacción
    $pdo->commit();
    
    echo "Migración completada.\n";
    echo "Usuarios actualizados: $updated\n";
    echo "Usuarios omitidos: $skipped\n";
    echo "Errores: $errors\n";
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
} 