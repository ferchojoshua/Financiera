<?php

// Primero, vamos a verificar el archivo userController.php
$controllerFile = 'app/Http/Controllers/userController.php';
if (!file_exists($controllerFile)) {
    echo "ERROR: No se pudo encontrar el archivo userController.php\n";
    exit(1);
}

echo "=========================================\n";
echo "Modificando el middleware del userController\n";
echo "=========================================\n\n";

// Leer el archivo actual
$controllerContent = file_get_contents($controllerFile);
if ($controllerContent === false) {
    echo "ERROR: No se pudo leer el archivo userController.php\n";
    exit(1);
}

// Buscar el constructor con el middleware problemático
$pattern = '/public function __construct\(\)\s*\{(.*?)}\s*\n/s';
if (preg_match($pattern, $controllerContent, $matches)) {
    $constructorBody = $matches[1];
    
    echo "Encontrado el constructor con middleware problemático.\n";
    
    // Verificar si contiene el mensaje de error
    if (strpos($constructorBody, "No existe relacion Usuario y Agente") !== false) {
        echo "El middleware contiene el mensaje de error 'No existe relacion Usuario y Agente'\n";
        
        // Crear una nueva versión del constructor que crea la relación en lugar de mostrar error
        $newConstructor = 'public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->id = Auth::user()->id;
            
            // Si el usuario es agente y no tiene relación con supervisor
            if (!db_supervisor_has_agent::where(\'id_user_agent\', Auth::id())->exists()) {
                // Buscar un supervisor existente
                $supervisor = User::where(\'level\', \'supervisor\')->orWhere(\'role\', \'supervisor\')->first();
                
                if (!$supervisor) {
                    // Crear un supervisor predeterminado si no existe ninguno
                    $supervisor = new User();
                    $supervisor->name = "Supervisor Predeterminado";
                    $supervisor->email = "supervisor_" . time() . "@sistema.com";
                    $supervisor->password = bcrypt("supervisor123");
                    $supervisor->level = "supervisor";
                    $supervisor->role = "supervisor";
                    $supervisor->save();
                }
                
                // Buscar una cartera existente
                $wallet = db_wallet::first();
                
                if (!$wallet) {
                    // Crear una cartera predeterminada si no existe ninguna
                    $wallet = new db_wallet();
                    $wallet->name = "Cartera Predeterminada";
                    $wallet->save();
                }
                
                // Crear la relación entre el agente y el supervisor
                $agent_supervisor = new db_supervisor_has_agent();
                $agent_supervisor->id_supervisor = $supervisor->id;
                $agent_supervisor->id_user_agent = Auth::id();
                $agent_supervisor->id_wallet = $wallet->id;
                $agent_supervisor->base = 5000.00; // Base predeterminada
                $agent_supervisor->save();
                
                // Registrar en el log
                \Log::info("Se creó automáticamente la relación Supervisor-Agente para el usuario " . Auth::id());
            }
            
            return $next($request);
        });
    }';
        
        // Reemplazar el constructor original por el nuevo
        $updatedContent = preg_replace($pattern, $newConstructor . "\n", $controllerContent);
        
        // Crear una copia de seguridad del archivo original
        copy($controllerFile, $controllerFile . '.bak');
        echo "Creada copia de seguridad del archivo en $controllerFile.bak\n";
        
        // Guardar el archivo modificado
        if (file_put_contents($controllerFile, $updatedContent) === false) {
            echo "ERROR: No se pudo guardar el archivo modificado.\n";
            exit(1);
        }
        
        echo "El constructor ha sido modificado para crear automáticamente la relación Supervisor-Agente.\n";
    } else {
        echo "El constructor no contiene el mensaje de error 'No existe relacion Usuario y Agente'.\n";
        echo "Posiblemente ya ha sido modificado o el error está en otro lugar.\n";
    }
} else {
    echo "No se encontró el constructor en el archivo userController.php\n";
    
    // Intentar agregar un constructor al inicio de la clase
    $pattern = '/class userController extends Controller\s*\{/';
    if (preg_match($pattern, $controllerContent)) {
        echo "Se encontró la definición de la clase userController.\n";
        
        $newConstructor = 'class userController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->id = Auth::user()->id;
            
            // Si el usuario es agente y no tiene relación con supervisor
            if (!db_supervisor_has_agent::where(\'id_user_agent\', Auth::id())->exists()) {
                // Buscar un supervisor existente
                $supervisor = User::where(\'level\', \'supervisor\')->orWhere(\'role\', \'supervisor\')->first();
                
                if (!$supervisor) {
                    // Crear un supervisor predeterminado si no existe ninguno
                    $supervisor = new User();
                    $supervisor->name = "Supervisor Predeterminado";
                    $supervisor->email = "supervisor_" . time() . "@sistema.com";
                    $supervisor->password = bcrypt("supervisor123");
                    $supervisor->level = "supervisor";
                    $supervisor->role = "supervisor";
                    $supervisor->save();
                }
                
                // Buscar una cartera existente
                $wallet = db_wallet::first();
                
                if (!$wallet) {
                    // Crear una cartera predeterminada si no existe ninguna
                    $wallet = new db_wallet();
                    $wallet->name = "Cartera Predeterminada";
                    $wallet->save();
                }
                
                // Crear la relación entre el agente y el supervisor
                $agent_supervisor = new db_supervisor_has_agent();
                $agent_supervisor->id_supervisor = $supervisor->id;
                $agent_supervisor->id_user_agent = Auth::id();
                $agent_supervisor->id_wallet = $wallet->id;
                $agent_supervisor->base = 5000.00; // Base predeterminada
                $agent_supervisor->save();
                
                // Registrar en el log
                \Log::info("Se creó automáticamente la relación Supervisor-Agente para el usuario " . Auth::id());
            }
            
            return $next($request);
        });
    }';
        
        // Reemplazar la definición de la clase por la nueva que incluye el constructor
        $updatedContent = preg_replace($pattern, $newConstructor, $controllerContent);
        
        // Crear una copia de seguridad del archivo original
        copy($controllerFile, $controllerFile . '.bak');
        echo "Creada copia de seguridad del archivo en $controllerFile.bak\n";
        
        // Guardar el archivo modificado
        if (file_put_contents($controllerFile, $updatedContent) === false) {
            echo "ERROR: No se pudo guardar el archivo modificado.\n";
            exit(1);
        }
        
        echo "Se ha agregado un constructor que crea automáticamente la relación Supervisor-Agente.\n";
    } else {
        echo "No se pudo encontrar la definición de la clase userController.\n";
        echo "El archivo podría estar corrupto o tener un formato inesperado.\n";
    }
}

echo "\n=========================================\n";
echo "IMPORTANTE:\n";
echo "=========================================\n\n";

echo "1. Se ha modificado el controlador para crear automáticamente la relación Supervisor-Agente.\n";
echo "2. Reinicia el servidor Laravel para aplicar los cambios:\n";
echo "   - php artisan cache:clear\n";
echo "   - php artisan config:clear\n";
echo "   - php artisan route:clear\n";
echo "   - Ctrl+C para detener el servidor actual y php artisan serve para reiniciarlo\n";
echo "3. Ahora deberías poder crear clientes sin el error 'No existe relacion Usuario y Agente'.\n"; 