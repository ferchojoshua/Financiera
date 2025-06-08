<?php
/**
 * Script para realizar un respaldo de la base de datos antes de la migración
 * Este script utiliza PDO para exportar la estructura y datos de las tablas
 */

// Configuración de la base de datos
$host = '127.0.0.1';
$db = 'sistema_prestamos';
$user = 'root';
$password = '';
$charset = 'utf8mb4';

// Directorio donde se guardarán los respaldos
$backupDir = __DIR__ . '/database/backups/';

// Crear el directorio si no existe
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "Directorio de respaldos creado: {$backupDir}\n";
}

// Nombre del archivo de respaldo (fecha y hora)
$date = date('Y-m-d_H-i-s');
$backupFile = $backupDir . "backup_{$db}_{$date}.sql";

try {
    // Conectar a la base de datos
    echo "Conectando a la base de datos...\n";
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $password, $options);
    echo "Conexión exitosa.\n";
    
    // Abrir el archivo para escribir
    $file = fopen($backupFile, 'w');
    if (!$file) {
        throw new Exception("No se pudo crear el archivo de respaldo: {$backupFile}");
    }
    
    // Escribir encabezado
    fwrite($file, "-- Sistema de Préstamos Database Backup\n");
    fwrite($file, "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n");
    
    // Obtener todas las tablas
    echo "Obteniendo lista de tablas...\n";
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    // Respaldar cada tabla
    foreach ($tables as $table) {
        echo "Respaldando tabla: {$table}\n";
        
        // Obtener la estructura de la tabla
        $result = $pdo->query("SHOW CREATE TABLE `{$table}`");
        $row = $result->fetch(PDO::FETCH_NUM);
        $createTable = $row[1];
        
        // Escribir DROP TABLE y CREATE TABLE
        fwrite($file, "DROP TABLE IF EXISTS `{$table}`;\n");
        fwrite($file, $createTable . ";\n\n");
        
        // Obtener los datos
        $result = $pdo->query("SELECT * FROM `{$table}`");
        $rowCount = $result->rowCount();
        
        if ($rowCount > 0) {
            // Comenzar el INSERT
            fwrite($file, "INSERT INTO `{$table}` VALUES\n");
            
            $counter = 0;
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $counter++;
                
                // Formatear los valores
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = $pdo->quote($value);
                    }
                }
                
                // Escribir los valores
                fwrite($file, "(" . implode(', ', $values) . ")");
                
                // Agregar coma o punto y coma según corresponda
                if ($counter < $rowCount) {
                    fwrite($file, ",\n");
                } else {
                    fwrite($file, ";\n\n");
                }
            }
        }
    }
    
    // Cerrar el archivo
    fclose($file);
    
    echo "Respaldo completado exitosamente: {$backupFile}\n";
    
    // Comprimir el archivo SQL
    echo "Comprimiendo el archivo de respaldo...\n";
    $zipFile = $backupFile . '.zip';
    
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($backupFile, basename($backupFile));
        $zip->close();
        
        // Eliminar el archivo SQL sin comprimir
        unlink($backupFile);
        
        echo "Archivo comprimido: {$zipFile}\n";
    } else {
        echo "Error al comprimir el archivo de respaldo.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Proceso de respaldo finalizado.\n"; 