<?php
/**
 * Script para buscar todas las referencias a 'amount' en los archivos de código
 */

echo "Buscando referencias a 'amount' en el código...\n";

// Directorios a buscar
$directories = [
    __DIR__ . '/app',
    __DIR__ . '/resources/views',
    __DIR__ . '/database/migrations'
];

// Extensiones de archivos a buscar
$extensions = ['php', 'blade.php'];

// Excepciones - nombres de archivo/directorios a ignorar
$ignore = [
    '.git',
    'vendor',
    'node_modules',
    'app/Models/Credit.php' // Ya lo hemos corregido
];

// Palabras clave a buscar
$keywords = [
    "'amount'",
    "\"amount\"",
    "->amount",
    "amount_neto",
    "sum('amount')",
    "sum(\"amount\")"
];

// Encontrar archivos recursivamente
function findFiles($dir, $extensions, $ignore) {
    $files = [];
    if (!is_dir($dir)) {
        return $files;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        $pathname = $file->getPathname();
        $shouldIgnore = false;
        
        // Verificar si el archivo/directorio debe ser ignorado
        foreach ($ignore as $ignoreItem) {
            if (strpos($pathname, $ignoreItem) !== false) {
                $shouldIgnore = true;
                break;
            }
        }
        
        if ($shouldIgnore) {
            continue;
        }
        
        // Verificar la extensión
        $ext = pathinfo($pathname, PATHINFO_EXTENSION);
        if (in_array($ext, $extensions) || in_array($pathname, $extensions)) {
            $files[] = $pathname;
        }
    }
    
    return $files;
}

// Buscar referencias en archivos
function searchInFiles($files, $keywords) {
    $results = [];
    
    foreach ($files as $file) {
        $content = file_get_contents($file);
        
        foreach ($keywords as $keyword) {
            if (strpos($content, $keyword) !== false) {
                // Encontrar las líneas donde aparece la palabra clave
                $lines = file($file);
                $lineMatches = [];
                
                foreach ($lines as $i => $line) {
                    if (strpos($line, $keyword) !== false) {
                        $lineNumber = $i + 1;
                        $lineMatches[] = "Línea {$lineNumber}: " . trim($line);
                    }
                }
                
                if (!empty($lineMatches)) {
                    if (!isset($results[$file])) {
                        $results[$file] = [];
                    }
                    $results[$file][$keyword] = $lineMatches;
                }
            }
        }
    }
    
    return $results;
}

// Buscar todos los archivos
$allFiles = [];
foreach ($directories as $dir) {
    $files = findFiles($dir, $extensions, $ignore);
    $allFiles = array_merge($allFiles, $files);
}

echo "Buscando en " . count($allFiles) . " archivos...\n";

// Buscar palabras clave
$results = searchInFiles($allFiles, $keywords);

// Mostrar resultados
if (empty($results)) {
    echo "No se encontraron referencias a 'amount'.\n";
} else {
    echo "Se encontraron referencias en los siguientes archivos:\n";
    
    foreach ($results as $file => $keywordMatches) {
        echo "\n===============================\n";
        echo "$file:\n";
        echo "===============================\n";
        
        foreach ($keywordMatches as $keyword => $lines) {
            echo "  Palabra clave: $keyword\n";
            foreach ($lines as $line) {
                echo "    $line\n";
            }
            echo "\n";
        }
    }
    
    echo "Total de archivos con referencias: " . count($results) . "\n";
} 