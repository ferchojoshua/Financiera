<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si existe la tabla role_permissions (para permisos específicos)
    $tableExists = $conn->query("SHOW TABLES LIKE 'role_permissions'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Crear la tabla de permisos específicos
        $sql = "CREATE TABLE IF NOT EXISTS `role_permissions` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `role_id` int UNSIGNED NOT NULL,
            `permission` varchar(100) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `role_permission_unique` (`role_id`, `permission`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        echo "Tabla role_permissions creada correctamente.\n";
    }
    
    // Definir los permisos específicos para los clientes
    $permissions = [
        'client.create',      // Crear clientes
        'client.edit',        // Editar clientes
        'client.view',        // Ver clientes
        'client.delete',      // Eliminar clientes
        'credit.create',      // Crear créditos
        'credit.view',        // Ver créditos
        'payment.create',     // Registrar pagos
        'payment.view'        // Ver pagos
    ];
    
    // Obtener los roles existentes
    $stmt = $conn->query("SELECT id, slug FROM roles");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $now = date('Y-m-d H:i:s');
    
    echo "Configurando permisos específicos para los siguientes roles:\n";
    foreach ($roles as $role) {
        echo "- " . $role['slug'] . " (ID: " . $role['id'] . ")\n";
        
        foreach ($permissions as $permission) {
            // Verificar si ya existe el permiso
            $stmt = $conn->prepare("SELECT * FROM role_permissions WHERE role_id = ? AND permission = ?");
            $stmt->execute([$role['id'], $permission]);
            $exists = $stmt->fetch();
            
            // Todos los roles de administrador, agente y supervisor deben tener estos permisos
            $grantPermission = in_array($role['slug'], ['superadmin', 'admin', 'agent', 'supervisor']);
            
            if (!$exists) {
                $stmt = $conn->prepare("INSERT INTO role_permissions (role_id, permission, created_at, updated_at) VALUES (?, ?, ?, ?)");
                $stmt->execute([$role['id'], $permission, $now, $now]);
                echo "  - Agregado permiso '$permission': " . ($grantPermission ? 'Concedido' : 'Denegado') . "\n";
            } else {
                // Actualizar el permiso existente
                $stmt = $conn->prepare("UPDATE role_permissions SET permission = ? WHERE role_id = ? AND permission = ?");
                $stmt->execute([$permission, $role['id'], $permission]);
                echo "  - Actualizado permiso '$permission': " . ($grantPermission ? 'Concedido' : 'Denegado') . "\n";
            }
        }
    }
    
    // Asegurarse de que el módulo clientes esté habilitado para todos los roles necesarios
    $roles_needed = ['superadmin', 'admin', 'agent', 'supervisor'];
    foreach ($roles as $role) {
        if (in_array($role['slug'], $roles_needed)) {
            $stmt = $conn->prepare("SELECT * FROM role_module_permissions WHERE role_id = ? AND module = 'clientes'");
            $stmt->execute([$role['id']]);
            $exists = $stmt->fetch();
            
            if (!$exists) {
                $stmt = $conn->prepare("INSERT INTO role_module_permissions (role_id, module, has_access, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$role['id'], 'clientes', 1, $now, $now]);
                echo "  - Agregado acceso al módulo 'clientes' para " . $role['slug'] . "\n";
            } else {
                $stmt = $conn->prepare("UPDATE role_module_permissions SET has_access = 1 WHERE role_id = ? AND module = 'clientes'");
                $stmt->execute([$role['id']]);
                echo "  - Actualizado acceso al módulo 'clientes' para " . $role['slug'] . "\n";
            }
        }
    }
    
    echo "\nPermisos específicos configurados correctamente.";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 