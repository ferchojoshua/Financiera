<?php
/**
 * Script para corregir el método overdue() en el ReportController
 */

echo "Iniciando corrección del método overdue() en ReportController...\n";

$controllerPath = __DIR__ . '/app/Http/Controllers/ReportController.php';

// Verificar si el archivo existe
if (!file_exists($controllerPath)) {
    echo "Error: No se encuentra el archivo del controlador en la ruta: $controllerPath\n";
    exit(1);
}

// Leer el contenido del archivo
$content = file_get_contents($controllerPath);

// Utilizar una expresión regular para encontrar el método overdue()
if (preg_match('/public\s+function\s+overdue\s*\(\s*Request\s+\$request\s*\).*?{(.*?)}/s', $content, $matches)) {
    $methodBody = $matches[1];
    
    echo "Método overdue() encontrado, buscando referencias a 'amount'...\n";
    
    // Verificar si hay referencias a 'amount' en el método
    $hasAmount = preg_match('/[\'"]amount[\'"]/', $methodBody);
    
    if ($hasAmount) {
        echo "Se encontraron referencias a 'amount', reemplazando por 'amount_neto'...\n";
        
        // Reemplazar todas las referencias a 'amount' por 'amount_neto'
        $fixedMethodBody = preg_replace('/([\'"])amount([\'"])/', '$1amount_neto$2', $methodBody);
        $fixedMethodBody = preg_replace('/->amount([^_a-zA-Z0-9])/', '->amount_neto$1', $fixedMethodBody);
        $fixedMethodBody = preg_replace('/sum\(([\'"])amount([\'"])\)/', 'sum($1amount_neto$2)', $fixedMethodBody);
        
        // Reemplazar el método en el contenido original
        $fixedContent = str_replace($methodBody, $fixedMethodBody, $content);
        
        // Crear una copia de seguridad del archivo original
        $backupPath = $controllerPath . '.overdue.bak';
        if (!copy($controllerPath, $backupPath)) {
            echo "Error: No se pudo crear una copia de seguridad del controlador.\n";
            exit(1);
        }
        echo "Se ha creado una copia de seguridad en: $backupPath\n";
        
        // Escribir el contenido modificado al archivo
        if (file_put_contents($controllerPath, $fixedContent) === false) {
            echo "Error: No se pudo escribir el archivo modificado.\n";
            exit(1);
        }
        
        echo "Corrección completada con éxito.\n";
    } else {
        echo "No se encontraron referencias directas a 'amount' en el método overdue().\n";
    }
} else {
    echo "Error: No se pudo encontrar el método overdue() en el controlador.\n";
    exit(1);
}

// Ahora, buscamos el error específico que causa el problema
echo "\nBuscando consultas SQL específicas con 'amount' en ReportController...\n";

// Patrones más específicos para buscar el error
$patterns = [
    '->sum\(\'amount\'\)',
    '->sum\("amount"\)',
    'sum\(\'amount\'\)',
    'sum\("amount"\)',
    'DB::raw\(\'sum\(amount\)\'\)',
    'DB::raw\("sum\(amount\)"\)',
];

foreach ($patterns as $pattern) {
    $count = preg_match_all('/' . preg_quote($pattern, '/') . '/', $content, $matches);
    if ($count > 0) {
        echo "Encontrado patrón '$pattern' $count veces.\n";
        
        // Reemplazar cada patrón
        $replacement = str_replace('amount', 'amount_neto', $pattern);
        $fixedContent = preg_replace('/' . preg_quote($pattern, '/') . '/', $replacement, $content);
        
        // Actualizar el contenido para el próximo patrón
        $content = $fixedContent;
        
        echo "Reemplazado '$pattern' por '$replacement'.\n";
    }
}

// Guardar los cambios finales
if (isset($fixedContent) && $fixedContent !== $content) {
    if (file_put_contents($controllerPath, $fixedContent) === false) {
        echo "Error: No se pudo escribir el archivo con los patrones corregidos.\n";
        exit(1);
    }
    echo "Se han aplicado correcciones adicionales.\n";
}

// Buscar el error específico en la consulta SQL
echo "\nBuscando consulta SQL específica que causa el error...\n";

// Este es el error específico que buscamos corregir
$errorPattern = "Unknown column 'amount' in 'field list' (SQL: select sum(`amount`) as aggregate from `credit`";

// Patrón para buscar la consulta sum('amount') en el método overdue
if (preg_match('/query->sum\(\'amount\'\)/', $content, $matches)) {
    echo "¡Encontrada la consulta problemática! Corrigiendo...\n";
    
    // Reemplazar la consulta
    $fixedContent = str_replace('query->sum(\'amount\')', 'query->sum(\'amount_neto\')', $content);
    
    // Guardar los cambios
    if (file_put_contents($controllerPath, $fixedContent) === false) {
        echo "Error: No se pudo escribir el archivo con la consulta corregida.\n";
        exit(1);
    }
    
    echo "Se ha corregido la consulta problemática.\n";
} else {
    echo "No se encontró la consulta problemática exacta.\n";
}

echo "\nProceso de corrección finalizado.\n"; 