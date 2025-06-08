<?php
/**
 * Script para configurar permisos detallados por módulo
 * utilizando la tabla role_module_permissions
 */

// Configuración de la base de datos
$host = '127.0.0.1';
$db = 'sistema_prestamos';
$user = 'root';
$password = '';
$charset = 'utf8mb4';

try {
    // Conectar a la base de datos
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $password, $options);
    echo "Conexión exitosa a la base de datos\n";
    
    // Verificar si existe la tabla role_module_permissions
    $tableExists = $pdo->query("SHOW TABLES LIKE 'role_module_permissions'")->rowCount() > 0;
    if (!$tableExists) {
        $pdo->exec("
            CREATE TABLE role_module_permissions (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                role_id BIGINT UNSIGNED NOT NULL,
                module VARCHAR(50) NOT NULL,
                has_access TINYINT(1) NOT NULL DEFAULT 0,
                permissions JSON NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX(role_id),
                INDEX(module),
                FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
                UNIQUE(role_id, module)
            )
        ");
        echo "Tabla role_module_permissions creada\n";
    } else {
        echo "La tabla role_module_permissions ya existe\n";
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Definir los módulos del sistema y sus permisos por rol
    $modulePermissions = [
        // Administrador - tiene acceso a todos los módulos
        'admin' => [
            'pymes' => true,
            'seguridad' => true,
            'rutas' => true,
            'caja' => true,
            'wallet' => true,
            'billetera' => true,
            'garantias' => true,
            'simulador' => true,
            'cobranza' => true,
            'auditoria' => true,
            'empresa' => true,
            'permisos' => true,
            'preferencias' => true,
            'reportes_cancelados' => true,
            'reportes_desembolsos' => true,
            'reportes_activos' => true,
            'reportes_vencidos' => true,
            'reportes_por_cancelar' => true,
            'solicitudes' => true,
            'analisis' => true,
            'productos' => true,
            'acuerdos' => true,
            'cierre_mes' => true,
            'recuperacion_desembolsos' => true,
            'asignacion_creditos' => true
        ],
        
        // Supervisor - acceso a módulos de supervisión
        'supervisor' => [
            'rutas' => true,
            'caja' => false,
            'cobranza' => true,
            'reportes' => true,
            'usuarios' => true,
            'clientes' => true,
            'creditos' => true,
            'pagos' => true,
            'auditoria' => false
        ],
        
        // Caja - acceso a módulos de caja
        'caja' => [
            'caja' => true,
            'pagos' => true,
            'reportes_diarios' => true
        ],
        
        // Colector - acceso a módulos de cobranza
        'Colector' => [
            'cobranza' => true,
            'clientes' => true,
            'creditos' => true,
            'pagos' => true,
            'rutas' => true
        ],
        
        // Contador - acceso a módulos de contabilidad
        'contador' => [
            'contabilidad' => true,
            'reportes' => true,
            'auditoria' => true
        ],
        
        // Cliente - acceso básico
        'user' => [
            'creditos_propios' => true,
            'pagos_propios' => true,
            'perfil' => true
        ]
    ];
    
    // Obtener todos los roles
    $roles = [];
    $result = $pdo->query("SELECT id, slug FROM roles WHERE is_active = 1");
    while ($row = $result->fetch()) {
        $roles[$row['slug']] = $row['id'];
    }
    
    // Preparar consulta para insertar o actualizar permisos
    $stmt = $pdo->prepare("
        INSERT INTO role_module_permissions 
            (role_id, module, has_access, created_at, updated_at) 
        VALUES 
            (?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE 
            has_access = VALUES(has_access),
            updated_at = NOW()
    ");
    
    // Configurar permisos para cada rol
    echo "Configurando permisos para los siguientes roles:\n";
    foreach ($modulePermissions as $roleSlug => $modules) {
        // Verificar si el rol existe
        if (!isset($roles[$roleSlug])) {
            echo "- Rol '{$roleSlug}' no encontrado, omitiendo.\n";
            continue;
        }
        
        $roleId = $roles[$roleSlug];
        echo "- {$roleSlug} (ID: {$roleId})\n";
        
        // Configurar permisos para cada módulo
        foreach ($modules as $module => $hasAccess) {
            $stmt->execute([$roleId, $module, $hasAccess ? 1 : 0]);
            echo "  - Módulo '{$module}' configurado para rol '{$roleSlug}' (acceso: " . ($hasAccess ? 'sí' : 'no') . ")\n";
        }
    }
    
    // Confirmar transacción
    $pdo->commit();
    
    echo "Configuración de permisos completada correctamente\n";
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
} 