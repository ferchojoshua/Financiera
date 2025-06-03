<?php
/**
 * Script para corregir específicamente la consulta SQL que causa el error
 */

echo "Iniciando corrección de la consulta SQL problemática...\n";

$controllerPath = __DIR__ . '/app/Http/Controllers/ReportController.php';

// Verificar si el archivo existe
if (!file_exists($controllerPath)) {
    echo "Error: No se encuentra el archivo del controlador en la ruta: $controllerPath\n";
    exit(1);
}

// Leer el contenido del archivo
$content = file_get_contents($controllerPath);

// El error específico que estamos buscando
$errorMessage = "SQLSTATE[42S22]: Column not found: 1054 Unknown column 'amount' in 'field list' (SQL: select sum(`amount`) as aggregate from `credit` where `status` = active and `disbursement_date` is not null and DATE_ADD(disbursement_date, INTERVAL 30 DAY) < CURDATE())";

// Identificar la parte relevante del error para buscar
$errorPattern = "select sum(`amount`) as aggregate from `credit` where `status` = active and `disbursement_date` is not null and DATE_ADD(disbursement_date, INTERVAL 30 DAY) < CURDATE()";

echo "Analizando el error: $errorMessage\n";

// Buscar patrones específicos relacionados con la consulta
$patterns = [
    // Buscar patrones directos de sum('amount')
    "sum\\('amount'\\)",
    "sum\\(\"amount\"\\)",
    
    // Buscar consultas SQL similares a la del error
    "where\\s+`status`\\s*=\\s*'active'.*?\\)\\s*->\\s*sum\\s*\\(\\s*'amount'\\s*\\)",
    "whereNotNull\\s*\\(\\s*'disbursement_date'\\s*\\).*?\\)\\s*->\\s*sum\\s*\\(\\s*'amount'\\s*\\)",
    
    // Buscar patrones dentro del método overdue()
    "overdue.*?total_overdue_amount.*?sum\\s*\\(\\s*'amount'\\s*\\)",
    
    // Patrones de Eloquent que podrían generar esta consulta
    "query->sum\\('amount'\\)",
    "\\->sum\\('amount'\\)"
];

$found = false;
$modified = false;

// Buscar y reemplazar los patrones
foreach ($patterns as $pattern) {
    echo "Buscando patrón: $pattern\n";
    
    if (preg_match_all("/$pattern/", $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $match) {
            $found = true;
            $matchText = $match[0];
            $offset = $match[1];
            
            echo "Encontrado: \"$matchText\" en la posición $offset\n";
            
            // Obtener el contexto alrededor del match (20 caracteres antes y después)
            $start = max(0, $offset - 50);
            $length = strlen($matchText) + 100;
            $context = substr($content, $start, $length);
            
            echo "Contexto: \"" . str_replace("\n", " ", $context) . "\"\n";
            
            // Reemplazar 'amount' por 'amount_neto' en el patrón encontrado
            $replacement = str_replace("'amount'", "'amount_neto'", $matchText);
            $replacement = str_replace('"amount"', '"amount_neto"', $replacement);
            
            echo "Reemplazando por: \"$replacement\"\n";
            
            // Realizar el reemplazo en el contenido
            $content = substr_replace($content, $replacement, $offset, strlen($matchText));
            $modified = true;
        }
    }
}

// Si no se encontró nada con los patrones, buscar directamente la consulta del error
if (!$found) {
    echo "No se encontraron patrones específicos. Buscando líneas con 'sum' y 'amount'...\n";
    
    // Dividir el contenido en líneas
    $lines = explode("\n", $content);
    $modifiedLines = [];
    
    foreach ($lines as $i => $line) {
        // Buscar líneas que contengan 'sum' y 'amount'
        if (strpos($line, 'sum') !== false && strpos($line, 'amount') !== false) {
            echo "Línea " . ($i + 1) . ": $line\n";
            
            // Reemplazar 'amount' por 'amount_neto' en esta línea
            $modifiedLine = str_replace("'amount'", "'amount_neto'", $line);
            $modifiedLine = str_replace('"amount"', '"amount_neto"', $modifiedLine);
            
            if ($modifiedLine !== $line) {
                echo "Reemplazando por: $modifiedLine\n";
                $modifiedLines[] = $i;
                $lines[$i] = $modifiedLine;
                $modified = true;
            }
        }
    }
    
    if (!empty($modifiedLines)) {
        $content = implode("\n", $lines);
    }
}

if ($modified) {
    // Crear una copia de seguridad del archivo original
    $backupPath = $controllerPath . '.query.bak';
    if (!copy($controllerPath, $backupPath)) {
        echo "Error: No se pudo crear una copia de seguridad del controlador.\n";
        exit(1);
    }
    echo "Se ha creado una copia de seguridad en: $backupPath\n";
    
    // Escribir el contenido modificado al archivo
    if (file_put_contents($controllerPath, $content) === false) {
        echo "Error: No se pudo escribir el archivo modificado.\n";
        exit(1);
    }
    
    echo "Corrección completada con éxito.\n";
} else {
    echo "No se encontraron ocurrencias exactas que coincidan con la consulta del error.\n";
    
    // Última opción: examinar el método overdue directamente
    echo "Buscando en el método overdue() completo...\n";
    
    if (preg_match('/public\s+function\s+overdue\s*\(\s*Request\s+\$request\s*\).*?{(.*?)}/s', $content, $matches)) {
        $methodBody = $matches[1];
        
        echo "Método overdue() encontrado, tamaño: " . strlen($methodBody) . " caracteres\n";
        echo "Primeros 100 caracteres: " . substr($methodBody, 0, 100) . "...\n";
        
        // Buscar cualquier referencia a 'amount' en el método
        $pattern = '/\b[\'"]?amount[\'"]?\b/';
        if (preg_match_all($pattern, $methodBody, $amountMatches)) {
            echo "Encontradas " . count($amountMatches[0]) . " referencias a 'amount':\n";
            
            foreach ($amountMatches[0] as $match) {
                echo "- $match\n";
            }
            
            // Reemplazar todas las referencias a 'amount' por 'amount_neto'
            $fixedMethodBody = preg_replace($pattern, 'amount_neto', $methodBody);
            
            // Reemplazar el método en el contenido original
            $fixedContent = str_replace($methodBody, $fixedMethodBody, $content);
            
            // Escribir el contenido modificado al archivo
            if (file_put_contents($controllerPath, $fixedContent) === false) {
                echo "Error: No se pudo escribir el archivo modificado.\n";
                exit(1);
            }
            
            echo "Se han reemplazado todas las referencias a 'amount' en el método overdue().\n";
        } else {
            echo "No se encontraron referencias a 'amount' en el método overdue().\n";
        }
    } else {
        echo "No se pudo extraer el método overdue() para un análisis detallado.\n";
    }
} 