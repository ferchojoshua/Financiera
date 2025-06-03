<?php

echo "=========================================\n";
echo "Verificación de middlewares de autorización\n";
echo "=========================================\n\n";

// Buscar middlewares en el archivo RouteServiceProvider
$routeServiceProvider = file_exists('app/Providers/RouteServiceProvider.php') 
    ? file_get_contents('app/Providers/RouteServiceProvider.php') 
    : null;

if ($routeServiceProvider) {
    echo "Analizando RouteServiceProvider.php para middlewares globales:\n";
    
    if (preg_match('/protected \$middleware\s*=\s*\[(.*?)\]/s', $routeServiceProvider, $matches)) {
        $middlewareList = $matches[1];
        echo "Middlewares globales encontrados:\n$middlewareList\n";
    } else {
        echo "No se encontraron middlewares globales definidos en RouteServiceProvider.\n";
    }
    
    echo "\n";
}

// Buscar middlewares en el archivo Kernel.php
$httpKernel = file_exists('app/Http/Kernel.php') 
    ? file_get_contents('app/Http/Kernel.php') 
    : null;

if ($httpKernel) {
    echo "Analizando Http/Kernel.php para grupos de middleware:\n";
    
    // Buscar middleware de grupos
    if (preg_match('/protected \$middlewareGroups\s*=\s*\[(.*?)\];/s', $httpKernel, $matches)) {
        $middlewareGroups = $matches[1];
        echo "Grupos de middleware encontrados:\n$middlewareGroups\n";
    } else {
        echo "No se encontraron grupos de middleware definidos en Kernel.php.\n";
    }
    
    // Buscar middleware de rutas
    if (preg_match('/protected \$routeMiddleware\s*=\s*\[(.*?)\];/s', $httpKernel, $matches)) {
        $routeMiddleware = $matches[1];
        echo "\nMiddlewares de ruta encontrados:\n$routeMiddleware\n";
    } else {
        echo "\nNo se encontraron middlewares de ruta definidos en Kernel.php.\n";
    }
    
    echo "\n";
}

// Buscar middleware en el archivo de rutas
$routesFile = file_exists('routes/web.php') 
    ? file_get_contents('routes/web.php') 
    : null;

if ($routesFile) {
    echo "Analizando rutas en web.php para middleware de autorización:\n";
    
    // Buscar middlewares aplicados a rutas de clientes
    if (preg_match_all('/Route::.*client.*->middleware\(\s*[\'"]([^\'"]*)[\'"]/', $routesFile, $matches)) {
        echo "Middlewares aplicados a rutas de clientes:\n";
        foreach ($matches[1] as $middleware) {
            echo "- $middleware\n";
        }
    } else {
        echo "No se encontraron middlewares directamente aplicados a rutas de clientes.\n";
    }
    
    // Buscar grupos de rutas con middleware
    if (preg_match_all('/Route::middleware\(\s*[\'"]([^\'"]*)[\'"].*?\)->group\(/s', $routesFile, $matches)) {
        echo "\nGrupos de rutas con middleware:\n";
        foreach ($matches[1] as $middleware) {
            echo "- $middleware\n";
        }
    } else {
        echo "\nNo se encontraron grupos de rutas con middleware.\n";
    }
    
    echo "\n";
}

// Buscar middlewares de autorización en el ClientController
$clientController = file_exists('app/Http/Controllers/ClientController.php') 
    ? file_get_contents('app/Http/Controllers/ClientController.php') 
    : null;

if ($clientController) {
    echo "Analizando ClientController.php para middleware de autorización:\n";
    
    // Buscar constructor con middleware
    if (preg_match('/function __construct.*?\{(.*?)\}/s', $clientController, $matches)) {
        $constructor = $matches[1];
        if (strpos($constructor, 'middleware') !== false) {
            echo "Middleware encontrado en el constructor del ClientController:\n$constructor\n";
        } else {
            echo "No se encontró middleware en el constructor del ClientController.\n";
        }
    } else {
        echo "No se encontró constructor en el ClientController.\n";
    }
    
    echo "\n";
}

echo "=========================================\n";
echo "RECOMENDACIONES ADICIONALES\n";
echo "=========================================\n\n";

echo "Para arreglar problemas de acceso a la creación de clientes:\n\n";

echo "1. Reinicia completamente el servidor Laravel (Ctrl+C y luego php artisan serve de nuevo)\n";
echo "2. Verifica el archivo .env para asegurarte que la configuración de la base de datos es correcta\n";
echo "3. Revisa los logs de Laravel en storage/logs/laravel.log para ver errores específicos\n";
echo "4. Prueba con un usuario nuevo o diferente para descartar problemas con tu sesión actual\n";
echo "5. Verifica que el modelo User tenga correctamente implementada la lógica de permisos\n";
echo "6. Revisa que el controlador de autenticación no esté bloqueando el acceso\n";
echo "7. Asegúrate de que la ruta /client/create está correctamente definida y accesible\n"; 