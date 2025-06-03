<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Configurar conexión a la base de datos
$db = app('db');

// Módulos del sistema
$modules = [
    'dashboard',
    'clientes',
    'creditos',
    'pagos',
    'cobranzas',
    'reportes',
    'configuracion',
    'usuarios',
    'contabilidad',
    'sucursales',
    'billeteras',
    'pymes',
    'garantias',
    'productos'
];

// Obtener todos los roles
$roles = $db->table('roles')->get();

$insertedCount = 0;

foreach ($roles as $role) {
    foreach ($modules as $module) {
        // Verificar si ya existe el permiso
        $exists = $db->table('role_module_permissions')
            ->where('role_id', $role->id)
            ->where('module', $module)
            ->exists();
        
        if (!$exists) {
            // Determinar si tiene acceso según el rol
            $hasAccess = ($role->slug === 'superadmin' || $role->slug === 'admin') ? true : false;
            
            // Casos especiales
            if ($role->slug === 'supervisor') {
                $hasAccess = in_array($module, ['dashboard', 'clientes', 'creditos', 'cobranzas', 'reportes']) ? true : false;
            } elseif ($role->slug === 'agent' || $role->slug === 'colector') {
                $hasAccess = in_array($module, ['dashboard', 'clientes', 'pagos', 'cobranzas']) ? true : false;
            } elseif ($role->slug === 'caja') {
                $hasAccess = in_array($module, ['dashboard', 'pagos', 'creditos']) ? true : false;
            }
            
            // Insertar el permiso
            $db->table('role_module_permissions')->insert([
                'role_id' => $role->id,
                'module' => $module,
                'has_access' => $hasAccess ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $insertedCount++;
        }
    }
}

echo "Se insertaron {$insertedCount} permisos de módulos para roles.\n"; 