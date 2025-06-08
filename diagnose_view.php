<?php
// Script de diagnóstico para verificar el layout utilizado

// Incluir el cargador de Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Iniciar la aplicación
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Configurar la respuesta como diagnóstico
header('Content-Type: text/html');

// Obtener la vista actual
$routeName = Route::currentRouteName();
$viewName = 'clients.index';

// Intentar renderizar la vista y capturar su salida
try {
    $view = view($viewName);
    
    // Determinar el layout actual
    $layoutUsed = "No determinado";
    $viewData = $view->getData();
    
    if (isset($viewData['_layout'])) {
        $layoutUsed = $viewData['_layout'];
    } else {
        // Buscar en la vista compilada
        $viewPath = app('view')->getFinder()->find($viewName);
        $viewContent = file_get_contents($viewPath);
        
        if (preg_match('/@extends\([\'"]([^\'"]+)[\'"]\)/', $viewContent, $matches)) {
            $layoutUsed = $matches[1];
        }
    }
    
    echo "<h1>Diagnóstico de Vista</h1>";
    echo "<p><strong>Vista actual:</strong> $viewName</p>";
    echo "<p><strong>Ruta actual:</strong> $routeName</p>";
    echo "<p><strong>Layout utilizado:</strong> $layoutUsed</p>";
    
    // Verificar configuración de rutas
    echo "<h2>Configuración de Rutas</h2>";
    $routeConfig = config('ui.route_layouts');
    echo "<pre>";
    print_r($routeConfig);
    echo "</pre>";
    
    // Verificar vista compilada
    echo "<h2>Contenido de la Vista</h2>";
    echo "<pre>";
    echo htmlspecialchars(substr($viewContent, 0, 500)) . "...";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h1>Error al cargar la vista</h1>";
    echo "<p>Mensaje: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . " en línea " . $e->getLine() . "</p>";
} 