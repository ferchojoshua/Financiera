<?php

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si existe la tabla role_module_permissions
    $tableExists = $conn->query("SHOW TABLES LIKE 'role_module_permissions'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Crear la tabla de permisos por módulo
        $sql = "CREATE TABLE IF NOT EXISTS `role_module_permissions` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `role_id` int UNSIGNED NOT NULL,
            `module` varchar(50) NOT NULL,
            `has_access` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `role_module_unique` (`role_id`, `module`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        echo "Tabla role_module_permissions creada correctamente.\n";
    }
    
    // Verificar si existe la tabla roles
    $tableExists = $conn->query("SHOW TABLES LIKE 'roles'")->rowCount() > 0;
    
    if (!$tableExists) {
        // Crear la tabla de roles
        $sql = "CREATE TABLE IF NOT EXISTS `roles` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `slug` varchar(255) NOT NULL,
            `description` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `roles_slug_unique` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        echo "Tabla roles creada correctamente.\n";
        
        // Insertar roles básicos
        $roles = [
            ['name' => 'Super Administrador', 'slug' => 'superadmin', 'description' => 'Acceso completo al sistema'],
            ['name' => 'Administrador', 'slug' => 'admin', 'description' => 'Administrador del sistema'],
            ['name' => 'Supervisor', 'slug' => 'supervisor', 'description' => 'Supervisor de agentes'],
            ['name' => 'Agente', 'slug' => 'agent', 'description' => 'Agente de cobranza'],
            ['name' => 'Cliente', 'slug' => 'client', 'description' => 'Cliente del sistema']
        ];
        
        $now = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("INSERT INTO roles (name, slug, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($roles as $role) {
            $stmt->execute([$role['name'], $role['slug'], $role['description'], $now, $now]);
        }
        
        echo "Roles básicos insertados correctamente.\n";
    }
    
    // Definir los módulos básicos del sistema
    $modules = ['dashboard', 'clientes', 'creditos', 'pagos', 'cobranzas', 'reportes', 'configuracion', 'usuarios', 'contabilidad', 'simulador'];
    
    // Obtener los roles existentes
    $stmt = $conn->query("SELECT id, slug FROM roles");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Configurando permisos para los siguientes roles:\n";
    foreach ($roles as $role) {
        echo "- " . $role['slug'] . " (ID: " . $role['id'] . ")\n";
        
        foreach ($modules as $module) {
            // Verificar si ya existe el permiso
            $stmt = $conn->prepare("SELECT * FROM role_module_permissions WHERE role_id = ? AND module = ?");
            $stmt->execute([$role['id'], $module]);
            $exists = $stmt->fetch();
            
            // Determinar si debe tener acceso por defecto
            $hasAccess = ($role['slug'] === 'superadmin' || $role['slug'] === 'admin') ? 1 : 0;
            
            // Para el simulador, permitir acceso a todos los roles
            if ($module === 'simulador') {
                $hasAccess = 1;
            }
            
            // Para clientes, permitir acceso a agentes y supervisores
            if ($module === 'clientes' && ($role['slug'] === 'agent' || $role['slug'] === 'supervisor')) {
                $hasAccess = 1;
            }
            
            if (!$exists) {
                $stmt = $conn->prepare("INSERT INTO role_module_permissions (role_id, module, has_access, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$role['id'], $module, $hasAccess, $now, $now]);
                echo "  - Agregado permiso para módulo '$module': " . ($hasAccess ? 'Concedido' : 'Denegado') . "\n";
            } else {
                // Actualizar el permiso existente si es un módulo especial
                if ($module === 'simulador' || ($module === 'clientes' && ($role['slug'] === 'agent' || $role['slug'] === 'supervisor'))) {
                    $stmt = $conn->prepare("UPDATE role_module_permissions SET has_access = ? WHERE role_id = ? AND module = ?");
                    $stmt->execute([$hasAccess, $role['id'], $module]);
                    echo "  - Actualizado permiso para módulo '$module': " . ($hasAccess ? 'Concedido' : 'Denegado') . "\n";
                }
            }
        }
    }
    
    echo "\nPermisos configurados correctamente.";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cerrar conexión
$conn = null; 