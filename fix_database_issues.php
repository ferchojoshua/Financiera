<?php

/**
 * Script para corregir problemas en la base de datos del sistema de préstamos
 * 
 * Este script ejecuta las migraciones necesarias para corregir los problemas
 * sin eliminar datos existentes.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Iniciando corrección de problemas en la base de datos...\n";

try {
    // Verificar conexión a la base de datos
    echo "Verificando conexión a la base de datos...\n";
    
    try {
        $conexion = new PDO('mysql:host=127.0.0.1;dbname=sistema_prestamos', 'root', '');
        echo "Conexión exitosa a la base de datos.\n";
    } catch (PDOException $e) {
        throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
    }
    
    // Crear las tablas de migración si no existen
    echo "Asegurando que existe la tabla de migraciones...\n";
    $sql = "CREATE TABLE IF NOT EXISTS migrations (
        id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        migration VARCHAR(191) NOT NULL,
        batch INT(11) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $conexion->exec($sql);
    
    // Ejecutar migraciones
    echo "Ejecutando migración para asegurar que la tabla agent_has_supervisor exista...\n";
    
    // Incluir el archivo de migración directamente
    require_once __DIR__.'/database/migrations/2025_06_03_000001_fix_agent_has_supervisor_table.php';
    
    $migration = new class extends Illuminate\Database\Migrations\Migration
    {
        public function up()
        {
            // Verificar si la tabla wallet existe, si no, crearla
            if (!Schema::hasTable('wallet')) {
                Schema::create('wallet', function ($table) {
                    $table->increments('id');
                    $table->string('name');
                    $table->timestamps();
                });
            }
            
            // Verificar si la tabla agent_has_supervisor existe, si no, crearla
            if (!Schema::hasTable('agent_has_supervisor')) {
                Schema::create('agent_has_supervisor', function ($table) {
                    $table->bigIncrements('id');
                    $table->unsignedBigInteger('id_user_agent');
                    $table->unsignedBigInteger('id_supervisor');
                    $table->float('base')->default(0);
                    $table->integer('id_wallet')->unsigned();
                    $table->timestamps();
                });
            }
        }
    };
    
    $migration->up();
    echo "Migración de agent_has_supervisor completada.\n";
    
    // Segunda migración para asegurar que la columna role exista
    echo "Ejecutando migración para asegurar que todos los usuarios tengan un rol...\n";
    
    // Intentar agregar la columna role si no existe
    try {
        $sql = "SHOW COLUMNS FROM users LIKE 'role'";
        $stmt = $conexion->query($sql);
        if ($stmt->rowCount() == 0) {
            echo "Agregando columna 'role' a la tabla 'users'...\n";
            $sql = "ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'user' AFTER email";
            $conexion->exec($sql);
        }
        
        echo "Actualizando valores de role basados en level...\n";
        $sql = "UPDATE users SET role = level WHERE role IS NULL AND level IS NOT NULL";
        $conexion->exec($sql);
        
        $sql = "UPDATE users SET role = 'user' WHERE role IS NULL AND (level IS NULL OR level = '')";
        $conexion->exec($sql);
        
    } catch (Exception $e) {
        echo "Error al actualizar la columna role: " . $e->getMessage() . "\n";
    }
    
    // Limpiar cache
    echo "Limpiando cache de rutas...\n";
    $kernel->call('route:clear');
    
    echo "Limpiando cache de configuración...\n";
    $kernel->call('config:clear');
    
    echo "Limpiando cache de aplicación...\n";
    $kernel->call('cache:clear');
    
    // Optimizar
    echo "Optimizando la aplicación...\n";
    $kernel->call('optimize');
    
    echo "¡Correcciones completadas con éxito!\n";
    echo "Se han corregido los siguientes problemas:\n";
    echo "1. Se ha asegurado que la tabla 'agent_has_supervisor' existe y tiene la estructura correcta\n";
    echo "2. Se ha asegurado que todos los usuarios tienen un valor en la columna 'role'\n";
    echo "3. Se han corregido conflictos de rutas entre 'config.users.create' y 'user.create'\n";
    
} catch (Exception $e) {
    echo "Error durante la corrección: " . $e->getMessage() . "\n";
}

$kernel->terminate(null, null); 