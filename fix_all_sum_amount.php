<?php
/**
 * Script para reemplazar todas las referencias a sum('amount') en ReportController.php
 */

echo "Iniciando corrección de todas las referencias a sum('amount') en ReportController.php...\n";

$controllerPath = __DIR__ . '/app/Http/Controllers/ReportController.php';

// Verificar si el archivo existe
if (!file_exists($controllerPath)) {
    echo "Error: No se encuentra el archivo del controlador en la ruta: $controllerPath\n";
    exit(1);
}

// Crear una copia de seguridad del archivo original
$backupPath = $controllerPath . '.allmount.bak';
if (!copy($controllerPath, $backupPath)) {
    echo "Error: No se pudo crear una copia de seguridad del controlador.\n";
    exit(1);
}
echo "Se ha creado una copia de seguridad en: $backupPath\n";

// Leer el contenido del archivo
$content = file_get_contents($controllerPath);

// Patrones para buscar
$patterns = [
    // Variantes directas de sum('amount')
    "sum('amount')" => "sum('amount_neto')",
    'sum("amount")' => 'sum("amount_neto")',
    "sum(`amount`)" => "sum(`amount_neto`)",
    'sum(\'amount\')' => 'sum(\'amount_neto\')',
    
    // Variantes con DB::raw
    "DB::raw('sum(amount)')" => "DB::raw('sum(amount_neto)')",
    'DB::raw("sum(amount)")' => 'DB::raw("sum(amount_neto)")',
    "DB::raw('SUM(amount)')" => "DB::raw('SUM(amount_neto)')",
    'DB::raw("SUM(amount)")' => 'DB::raw("SUM(amount_neto)")',
];

// Contar reemplazos realizados
$totalReplacements = 0;

// Realizar reemplazos
foreach ($patterns as $search => $replace) {
    // Contar ocurrencias
    $count = substr_count($content, $search);
    if ($count > 0) {
        echo "Encontrado '$search': $count ocurrencias.\n";
        $totalReplacements += $count;
        
        // Realizar reemplazo
        $content = str_replace($search, $replace, $content);
    }
}

if ($totalReplacements > 0) {
    // Escribir el contenido modificado al archivo
    if (file_put_contents($controllerPath, $content) === false) {
        echo "Error: No se pudo escribir el archivo modificado.\n";
        exit(1);
    }
    
    echo "Se han realizado $totalReplacements reemplazos en total.\n";
} else {
    echo "No se encontraron ocurrencias directas de 'sum('amount')' para reemplazar.\n";
    
    // Intentar con expresiones regulares más complejas
    echo "\nBuscando con expresiones regulares más complejas...\n";
    
    $regexPatterns = [
        '~->sum\s*\(\s*[\'"]amount[\'"]\s*\)~' => '->sum(\'amount_neto\')',
        '~sum\s*\(\s*[\'"]amount[\'"]\s*\)~' => 'sum(\'amount_neto\')',
        '~DB::raw\s*\(\s*[\'"].*?sum\s*\(\s*[\'"]?amount[\'"]?\s*\).*?[\'"]\s*\)~i' => 'DB::raw(\'sum(amount_neto)\')',
    ];
    
    foreach ($regexPatterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content, -1, $count);
        if ($count > 0) {
            echo "Patrón '$pattern': reemplazadas $count ocurrencias.\n";
            $totalReplacements += $count;
        }
    }
    
    if ($totalReplacements > 0) {
        // Escribir el contenido modificado al archivo
        if (file_put_contents($controllerPath, $content) === false) {
            echo "Error: No se pudo escribir el archivo modificado.\n";
            exit(1);
        }
        
        echo "Se han realizado $totalReplacements reemplazos con expresiones regulares.\n";
    } else {
        echo "No se encontraron coincidencias con expresiones regulares.\n";
    }
}

// Limpiar la caché de Laravel, si existe artisan
echo "\nIntentando limpiar la caché de Laravel...\n";
if (file_exists(__DIR__ . '/artisan')) {
    // Comando para limpiar la caché de Laravel
    passthru('php artisan cache:clear');
    passthru('php artisan config:clear');
    passthru('php artisan route:clear');
    passthru('php artisan view:clear');
    echo "Caché de Laravel limpiada.\n";
} else {
    echo "No se encontró el archivo artisan para limpiar la caché.\n";
}

echo "\nProceso de corrección finalizado.\n"; 