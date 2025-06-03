<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Sistema de Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 40px 0;
        }
        .error-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .error-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .error-header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
        }
        .error-body {
            padding: 30px;
        }
        .error-message {
            font-size: 18px;
            margin-bottom: 20px;
            color: #dc3545;
        }
        .error-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            font-family: monospace;
            overflow-x: auto;
        }
        .actions {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container error-container">
        <div class="card error-card">
            <div class="error-header">
                <h1><i class="fa fa-exclamation-triangle"></i> Error en el Sistema</h1>
            </div>
            <div class="error-body">
                <div class="error-message">
                    {{ $message ?? 'Ha ocurrido un error inesperado.' }}
                </div>
                
                @if(isset($details))
                <div class="error-details">
                    {{ $details }}
                </div>
                @endif
                
                <div class="actions">
                    <a href="{{ url('/') }}" class="btn btn-primary">Volver al Inicio</a>
                    <a href="javascript:history.back()" class="btn btn-secondary">Volver Atrás</a>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <p>Por favor, contacte al administrador del sistema si este error persiste.</p>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 