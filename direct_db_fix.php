<?php
/**
 * Script para probar y depurar la consulta SQL que está fallando
 */

// Configurar la conexión a la base de datos directamente
$host = '127.0.0.1';
$database = 'sistema_prestamos';
$username = 'root';
$password = '';

try {
    echo "Conectando a la base de datos: $database en $host\n";
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión establecida correctamente.\n";
    
    // Verificar la estructura de la tabla credit
    echo "\nVerificando estructura de la tabla 'credit':\n";
    $stmt = $pdo->query("DESCRIBE credit");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Columnas encontradas en la tabla credit:\n";
    $hasAmount = false;
    $hasAmountNeto = false;
    
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        if ($column['Field'] === 'amount') {
            $hasAmount = true;
        }
        if ($column['Field'] === 'amount_neto') {
            $hasAmountNeto = true;
        }
    }
    
    if (!$hasAmount && $hasAmountNeto) {
        echo "\nLa tabla 'credit' NO tiene la columna 'amount', pero SÍ tiene 'amount_neto'.\n";
    } elseif ($hasAmount && $hasAmountNeto) {
        echo "\nLa tabla 'credit' tiene AMBAS columnas: 'amount' y 'amount_neto'.\n";
    } elseif ($hasAmount && !$hasAmountNeto) {
        echo "\nLa tabla 'credit' tiene 'amount', pero NO tiene 'amount_neto'.\n";
    } else {
        echo "\nLa tabla 'credit' NO tiene ninguna de las columnas 'amount' o 'amount_neto'.\n";
    }
    
    // Intentar ejecutar la consulta problemática
    echo "\nIntentando ejecutar la consulta problemática...\n";
    
    try {
        $stmt = $pdo->query("
            SELECT sum(`amount_neto`) as total 
            FROM `credit` 
            WHERE `status` = 'active' 
            AND `disbursement_date` IS NOT NULL 
            AND DATE_ADD(disbursement_date, INTERVAL 30 DAY) < CURDATE()
        ");
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Consulta ejecutada correctamente usando 'amount_neto'.\n";
        echo "Total: " . ($result['total'] ?? 'NULL') . "\n";
    } catch (PDOException $e) {
        echo "Error al ejecutar la consulta con 'amount_neto': " . $e->getMessage() . "\n";
    }
    
    // Intentar ejecutar la consulta original que falla
    echo "\nIntentando ejecutar la consulta original (la que falla)...\n";
    
    try {
        $stmt = $pdo->query("
            SELECT sum(`amount`) as total 
            FROM `credit` 
            WHERE `status` = 'active' 
            AND `disbursement_date` IS NOT NULL 
            AND DATE_ADD(disbursement_date, INTERVAL 30 DAY) < CURDATE()
        ");
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Consulta ejecutada correctamente usando 'amount'.\n";
        echo "Total: " . ($result['total'] ?? 'NULL') . "\n";
    } catch (PDOException $e) {
        echo "Error al ejecutar la consulta con 'amount': " . $e->getMessage() . "\n";
    }
    
    // Verificar el controlador
    $controllerPath = __DIR__ . '/app/Http/Controllers/ReportController.php';
    if (file_exists($controllerPath)) {
        echo "\nBuscando consultas problemáticas en el controlador...\n";
        $content = file_get_contents($controllerPath);
        
        // Buscar patrones específicos
        $pattern = '~sum\s*\(\s*[\'"]amount[\'"]\s*\)~';
        if (preg_match_all($pattern, $content, $matches)) {
            echo "Encontradas " . count($matches[0]) . " referencias a sum('amount').\n";
            
            foreach ($matches[0] as $i => $match) {
                echo "- " . ($i+1) . ": $match\n";
            }
        } else {
            echo "No se encontraron referencias a sum('amount')\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage() . "\n";
    exit(1); 