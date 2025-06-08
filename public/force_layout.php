<?php
// Script para forzar layout master en la aplicación

// Mensaje de inicio
echo "<h1>Forzando layout master</h1>";

// Verificar si tenemos permisos de escritura
$configPath = __DIR__ . '/../app/Providers/AppServiceProvider.php';
if (!is_writable($configPath)) {
    echo "<p style='color:red'>ERROR: No se tiene permiso de escritura en el archivo de configuración.</p>";
    exit;
}

// Leer el archivo actual
$content = file_get_contents($configPath);

// Verificar si ya contiene la configuración
if (strpos($content, 'boot_layout_fix') !== false) {
    echo "<p>La configuración ya ha sido aplicada.</p>";
} else {
    // Agregar método de configuración
    $newMethod = <<<'EOD'
    
    /**
     * Corrección temporal para forzar el layout master
     */
    private function boot_layout_fix()
    {
        // Forzar el uso del layout master para clientes
        \Illuminate\Support\Facades\View::composer('clients.*', function ($view) {
            $view->with('_layout', 'layouts.master');
        });
        
        // Sobrescribir método view() para rutas específicas
        $originalViewFinder = app('view.finder');
        app()->bind('view.finder', function ($app) use ($originalViewFinder) {
            $request = $app->make('request');
            
            // Si estamos en /clients, forzar el layout master
            if ($request->is('clients') || $request->is('clients/*')) {
                // Forzar el uso del layout master
                \Illuminate\Support\Facades\View::share('_layout', 'layouts.master');
            }
            
            return $originalViewFinder;
        });
    }
EOD;

    // Insertar el nuevo método después de boot()
    $pattern = '/(public function boot\(\)[^{]*{[^}]+})/s';
    $replacement = '$1' . $newMethod;
    $content = preg_replace($pattern, $replacement, $content);
    
    // Agregar la llamada al método en boot()
    $pattern = '/(public function boot\(\)[^{]*{)/';
    $replacement = '$1' . PHP_EOL . '        $this->boot_layout_fix();';
    $content = preg_replace($pattern, $replacement, $content);
    
    // Guardar el archivo modificado
    file_put_contents($configPath, $content);
    
    echo "<p style='color:green'>Configuración aplicada correctamente.</p>";
    echo "<p>Por favor, reinicia el servidor web para que los cambios surtan efecto.</p>";
}

// Mostrar opciones
echo "<div style='margin-top:20px'>";
echo "<a href='/clients' style='padding:10px; background:#3f51b5; color:white; text-decoration:none; margin-right:10px;'>Ir a Clientes</a>";
echo "<a href='/clients-test' style='padding:10px; background:#4caf50; color:white; text-decoration:none;'>Ir a Prueba</a>";
echo "</div>"; 