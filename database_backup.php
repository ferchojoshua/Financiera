<?php
/**
 * Script para realizar un respaldo de la base de datos antes de la migración
 * Este script utiliza mysqldump para crear un archivo .sql con la estructura y datos
 */

// Configuración de la base de datos (desde config/database.php)
$host = '127.0.0.1';
$db = 'sistema_prestamos';
$user = 'root';
$password = '';

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

// Comando para realizar el respaldo
$command = "mysqldump --host={$host} --user={$user}";

// Añadir contraseña si existe
if (!empty($password)) {
    $command .= " --password={$password}";
}

// Completar el comando
$command .= " {$db} > {$backupFile}";

// Ejecutar el comando
echo "Iniciando respaldo de la base de datos...\n";
system($command, $returnVar);

// Verificar si el respaldo fue exitoso
if ($returnVar === 0) {
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
} else {
    echo "Error al realizar el respaldo. Código de error: {$returnVar}\n";
    echo "Por favor, verifique que mysqldump esté instalado y accesible.\n";
}

echo "Proceso de respaldo finalizado.\n"; 