<?php
// Script para diagnosticar el problema de layout

// Definir ruta base para incluir los archivos correctos
define('LARAVEL_START', microtime(true));

// Cargamos la aplicación Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Ahora tenemos acceso a las funciones de Laravel
echo "<h1>Diagnóstico de Layout</h1>";

// Verificar layouts disponibles
echo "<h2>Layouts disponibles</h2>";
$layoutsPath = __DIR__ . '/../resources/views/layouts';
$layouts = scandir($layoutsPath);
echo "<ul>";
foreach ($layouts as $layout) {
    if ($layout != '.' && $layout != '..') {
        echo "<li>{$layout}</li>";
    }
}
echo "</ul>";

// Verificar vista clients/index.blade.php
echo "<h2>Vista clients/index.blade.php</h2>";
$clientsIndexPath = __DIR__ . '/../resources/views/clients/index.blade.php';
if (file_exists($clientsIndexPath)) {
    $content = file_get_contents($clientsIndexPath);
    $layout = preg_match('/@extends\([\'"]([^\'"]+)[\'"]\)/', $content, $matches) ? $matches[1] : "No encontrado";
    echo "<p>Layout usado: {$layout}</p>";
    
    // Mostrar las primeras líneas
    echo "<pre>";
    echo htmlspecialchars(substr($content, 0, 300)) . "...";
    echo "</pre>";
} else {
    echo "<p>La vista no existe</p>";
}

// Verificar configuración de UI
echo "<h2>Configuración UI</h2>";
$uiConfig = config('ui');
echo "<pre>";
print_r($uiConfig);
echo "</pre>";

// Verificar rutas para clients.index
echo "<h2>Ruta para clients.index</h2>";
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    if ($route->getName() == 'clients.index') {
        echo "<p>Método: " . implode('|', $route->methods()) . "</p>";
        echo "<p>URI: " . $route->uri() . "</p>";
        echo "<p>Acción: " . $route->getActionName() . "</p>";
        echo "<p>Middleware: " . implode(', ', $route->middleware()) . "</p>";
        break;
    }
}

// Intentar renderizar la vista directamente
echo "<h2>Intento de renderizar vista</h2>";
try {
    $view = view('clients.index')->with([
        'clients' => app('App\Models\Client')->paginate(10),
        'search' => '',
        'status' => 'active'
    ])->render();
    
    echo "<p>Resultado: Vista renderizada correctamente</p>";
} catch (\Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . " en línea " . $e->getLine() . "</p>";
} 