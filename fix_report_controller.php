<?php
/**
 * Script para corregir las referencias a la columna amount en el controlador ReportController
 */

echo "Iniciando corrección del controlador ReportController...\n";

$controllerPath = __DIR__ . '/app/Http/Controllers/ReportController.php';

// Verificar si el archivo existe
if (!file_exists($controllerPath)) {
    echo "Error: No se encuentra el archivo del controlador en la ruta: $controllerPath\n";
    exit(1);
}

// Leer el contenido del archivo
$content = file_get_contents($controllerPath);

// Reemplazar todas las referencias a 'amount' por 'amount_neto'
$replacements = [
    // Referencias en consultas sum
    "'sum\\('amount'\\)" => "'sum('amount_neto')",
    "sum\\('amount'\\)" => "sum('amount_neto')",
    "\\->sum\\('amount'\\)" => "->sum('amount_neto')",
    
    // Referencias en consultas avg
    "'avg\\('amount'\\)" => "'avg('amount_neto')",
    "avg\\('amount'\\)" => "avg('amount_neto')",
    "\\->avg\\('amount'\\)" => "->avg('amount_neto')",
    
    // Referencias en estadísticas
    "'total_amount' => \\$credits->sum\\('amount'\\)" => "'total_amount' => \$credits->sum('amount_neto')",
    "'total_amount' => \\$query->sum\\('amount'\\)" => "'total_amount' => \$query->sum('amount_neto')",
    "'avg_amount' => \\$credits->count\\(\\) > 0 \\? \\$credits->sum\\('amount'\\) / \\$credits->count\\(\\) : 0" => 
    "'avg_amount' => \$credits->count() > 0 ? \$credits->sum('amount_neto') / \$credits->count() : 0",
    
    // Referencias en más estadísticas
    "'total_overdue_amount' => \\$query->sum\\('amount'\\)" => "'total_overdue_amount' => \$query->sum('amount_neto')",
    
    // Referencias a 'interest_amount' (si existe este problema)
    "'interest_amount'" => "'utility'",
    "interest_amount" => "utility"
];

$modified = $content;
foreach ($replacements as $search => $replace) {
    $modified = preg_replace("/$search/", $replace, $modified);
}

// Verificar si se hicieron cambios
if ($modified === $content) {
    echo "No se encontraron referencias a 'amount' para corregir en el controlador.\n";
    exit(0);
}

// Crear una copia de seguridad del archivo original
$backupPath = $controllerPath . '.bak';
if (!copy($controllerPath, $backupPath)) {
    echo "Error: No se pudo crear una copia de seguridad del controlador.\n";
    exit(1);
}
echo "Se ha creado una copia de seguridad en: $backupPath\n";

// Escribir el contenido modificado al archivo
if (file_put_contents($controllerPath, $modified) === false) {
    echo "Error: No se pudo escribir el archivo modificado.\n";
    exit(1);
}

echo "Corrección completada con éxito. Se reemplazaron las referencias a 'amount' por 'amount_neto'.\n";
echo "También se reemplazaron las referencias a 'interest_amount' por 'utility'.\n"; 