<!DOCTYPE html>
<html>
<head>
    <title>Prueba de Vista</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1>Prueba de Vista</h1>
            </div>
            <div class="card-body">
                <p>Esta es una página de prueba para verificar si las vistas están funcionando correctamente.</p>
                
                <div class="alert alert-info">
                    <strong>Información de diagnóstico:</strong>
                    <ul>
                        <li>Laravel versión: {{ app()->version() }}</li>
                        <li>PHP versión: {{ phpversion() }}</li>
                        <li>Servidor web: {{ isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'No disponible' }}</li>
                        <li>Tiempo de carga: {{ round(microtime(true) - LARAVEL_START, 3) }} segundos</li>
                    </ul>
                </div>
                
                <div class="mt-4">
                    <h3>Enlaces de prueba:</h3>
                    <div class="list-group">
                        <a href="{{ url('/client/create') }}" class="list-group-item list-group-item-action">Vista original de cliente</a>
                        <a href="{{ url('/client/create-simple') }}" class="list-group-item list-group-item-action">Vista simple de cliente</a>
                        <a href="{{ url('/client/minimal') }}" class="list-group-item list-group-item-action">Vista mínima de cliente</a>
                        <a href="{{ url('/ensure-main-branch') }}" class="list-group-item list-group-item-action">Crear sucursal principal (si no existe)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log('Vista de prueba cargada correctamente');
    </script>
</body>
</html> 