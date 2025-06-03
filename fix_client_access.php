<?php

echo "=========================================\n";
echo "Corrección de acceso a crear clientes\n";
echo "=========================================\n\n";

$routesFile = 'routes/web.php';

// Leer el archivo actual
$currentRoutes = file_get_contents($routesFile);
if ($currentRoutes === false) {
    echo "ERROR: No se pudo leer el archivo de rutas web.php\n";
    exit(1);
}

// El problema principal parece ser que las rutas de cliente están restringidas por middleware
// Vamos a buscar y modificar las rutas específicas para client/create

// Patrón 1: Buscar rutas para clientes dentro del grupo de middleware 'agent'
$pattern1 = "/Route::middleware\(\['auth', 'agent'\]\)->group\(function \(\) \{(.*?)}\);/s";
if (preg_match($pattern1, $currentRoutes, $matches)) {
    $agentRoutes = $matches[1];
    echo "Encontradas rutas con middleware 'agent'.\n";
    
    // Verificar si hay rutas para client/create
    if (strpos($agentRoutes, "Route::get('/client/create'") !== false) {
        echo "La ruta client/create está protegida por el middleware 'agent'.\n";
        echo "Esto significa que SÓLO los usuarios con rol 'agent' pueden acceder.\n\n";
    }
}

// Crear un nuevo grupo de rutas accesible para todos los usuarios autenticados
$newClientRoutes = "
// Rutas de cliente accesibles para todos los usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/client/create', 'ClientController@create')->name('client.create');
    Route::post('/client', 'ClientController@store')->name('client.store');
    Route::get('/client', 'ClientController@index')->name('client.index');
});
";

// Crear una copia de seguridad del archivo routes/web.php
copy($routesFile, $routesFile . '.bak');
echo "Creada copia de seguridad del archivo de rutas en $routesFile.bak\n\n";

// Crear un nuevo archivo de rutas corregido
// Lo hacemos insertando las nuevas rutas justo después de Auth::routes();
$updatedRoutes = preg_replace(
    '/Auth::routes\(\);/',
    "Auth::routes();\n\n" . $newClientRoutes,
    $currentRoutes
);

if ($updatedRoutes === null) {
    echo "ERROR: No se pudo modificar el archivo de rutas.\n";
    exit(1);
}

// Eliminar rutas duplicadas (las que están en el grupo middleware agent)
$updatedRoutes = preg_replace(
    "/Route::middleware\(\['auth', 'agent'\]\)->group\(function \(\) \{\s*Route::get\('\/client\/create',.*?\);\s*Route::get\('\/client',.*?\);\s*/s",
    "Route::middleware(['auth', 'agent'])->group(function () {\n    ",
    $updatedRoutes
);

// Guardar el archivo actualizado
if (file_put_contents($routesFile, $updatedRoutes) === false) {
    echo "ERROR: No se pudo guardar el archivo de rutas modificado.\n";
    exit(1);
}

echo "Archivo de rutas actualizado correctamente.\n";
echo "Ahora las rutas de cliente son accesibles para todos los usuarios autenticados.\n\n";

echo "=========================================\n";
echo "IMPORTANTE: Reiniciar el servidor Laravel\n";
echo "=========================================\n\n";

echo "Para que los cambios surtan efecto, debes reiniciar el servidor Laravel:\n";
echo "1. Presiona Ctrl+C para detener el servidor actual.\n";
echo "2. Ejecuta 'php artisan route:clear' para limpiar la caché de rutas.\n";
echo "3. Ejecuta 'php artisan serve' para reiniciar el servidor.\n\n";

echo "Después de reiniciar, prueba acceder a:\n";
echo "http://127.0.0.1:8000/client/create\n";
echo "http://127.0.0.1:8000/client\n"; 