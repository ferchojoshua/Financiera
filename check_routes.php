<?php

echo "=========================================\n";
echo "Verificación de rutas para clientes y créditos\n";
echo "=========================================\n\n";

// Obtener el contenido del archivo de rutas web
$routesFile = file_get_contents('routes/web.php');

if ($routesFile === false) {
    echo "No se pudo leer el archivo de rutas web.php\n";
    exit(1);
}

// Buscar rutas relacionadas con clientes
echo "Rutas relacionadas con clientes:\n";
$clientRoutes = [];
preg_match_all('/Route::(get|post|put|delete)\s*\(\s*[\'"]([^\'"]*client[^\'"]*)[\'"]/', $routesFile, $matches, PREG_SET_ORDER);

if (empty($matches)) {
    echo "- No se encontraron rutas de cliente en web.php\n";
} else {
    foreach ($matches as $match) {
        $method = strtoupper($match[1]);
        $route = $match[2];
        echo "- [$method] $route\n";
        $clientRoutes[] = $route;
    }
}

// Buscar rutas relacionadas con créditos
echo "\nRutas relacionadas con créditos:\n";
preg_match_all('/Route::(get|post|put|delete)\s*\(\s*[\'"]([^\'"]*credit[^\'"]*)[\'"]/', $routesFile, $matches, PREG_SET_ORDER);

if (empty($matches)) {
    echo "- No se encontraron rutas de crédito en web.php\n";
} else {
    foreach ($matches as $match) {
        $method = strtoupper($match[1]);
        $route = $match[2];
        echo "- [$method] $route\n";
    }
}

echo "\n=========================================\n";
echo "Verificación de controllers para clientes\n";
echo "=========================================\n\n";

// Verificar si existen los controladores
$controllerPath = 'app/Http/Controllers/ClientController.php';
if (file_exists($controllerPath)) {
    echo "El controlador ClientController existe.\n";
    
    // Verificar métodos del controlador
    $controllerContent = file_get_contents($controllerPath);
    
    if (strpos($controllerContent, 'function create') !== false) {
        echo "- Método create() encontrado en ClientController\n";
    } else {
        echo "- ¡ALERTA! Método create() NO encontrado en ClientController\n";
    }
    
    if (strpos($controllerContent, 'function store') !== false) {
        echo "- Método store() encontrado en ClientController\n";
    } else {
        echo "- ¡ALERTA! Método store() NO encontrado en ClientController\n";
    }
    
    if (strpos($controllerContent, 'function index') !== false) {
        echo "- Método index() encontrado en ClientController\n";
    } else {
        echo "- ¡ALERTA! Método index() NO encontrado en ClientController\n";
    }
} else {
    echo "¡ALERTA! El controlador ClientController NO existe.\n";
}

echo "\n=========================================\n";
echo "Verificación de vistas para clientes\n";
echo "=========================================\n\n";

// Verificar vistas de cliente
$clientCreateView = 'resources/views/client/create.blade.php';
if (file_exists($clientCreateView)) {
    echo "La vista client/create.blade.php existe.\n";
} else {
    echo "¡ALERTA! La vista client/create.blade.php NO existe.\n";
}

$clientIndexView = 'resources/views/client/index.blade.php';
if (file_exists($clientIndexView)) {
    echo "La vista client/index.blade.php existe.\n";
} else {
    echo "¡ALERTA! La vista client/index.blade.php NO existe.\n";
}

echo "\n=========================================\n";
echo "SOLUCIÓN RECOMENDADA\n";
echo "=========================================\n\n";

echo "Si estás experimentando problemas para acceder a la página de creación de clientes, haz lo siguiente:\n\n";

echo "1. Verifica que las URLs mostradas anteriormente coincidan con las que están usando en tu navegador.\n";
echo "2. Asegúrate de que has reiniciado el servidor Laravel después de los cambios en los permisos.\n";
echo "3. Prueba limpiar la caché de Laravel con estos comandos:\n";
echo "   - php artisan config:clear\n";
echo "   - php artisan cache:clear\n";
echo "   - php artisan view:clear\n";
echo "   - php artisan route:clear\n";
echo "4. Verifica que no haya un middleware de autorización bloqueando el acceso.\n";
echo "5. Comprueba que el servidor web tenga los permisos adecuados para los archivos del proyecto.\n"; 