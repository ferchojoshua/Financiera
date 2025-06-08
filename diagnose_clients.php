<?php
// Archivo de diagnóstico para identificar problemas con vistas

// Mostrar información básica
echo "Diagnóstico del Sistema de Clientes<br>";
echo "-----------------------------------<br>";
echo "Fecha/Hora: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br><br>";

// Verificar archivos de vistas
echo "Verificando archivos de vistas:<br>";
echo "------------------------------<br>";

$viewsPath = __DIR__ . '/resources/views';

// Verificar vista clients/index.blade.php
$clientsIndexPath = $viewsPath . '/clients/index.blade.php';
$clientIndexPath = $viewsPath . '/client/index.blade.php';

echo "Vista clients/index.blade.php: " . (file_exists($clientsIndexPath) ? "Existe" : "No existe") . "<br>";
echo "Vista client/index.blade.php: " . (file_exists($clientIndexPath) ? "Existe" : "No existe") . "<br><br>";

// Verificar contenido de la vista clients/index.blade.php
if (file_exists($clientsIndexPath)) {
    $content = file_get_contents($clientsIndexPath);
    $layout = preg_match('/@extends\([\'"]([^\'"]+)[\'"]\)/', $content, $matches) ? $matches[1] : "No encontrado";
    echo "Layout usado en clients/index.blade.php: " . $layout . "<br>";
}

// Verificar contenido de la vista client/index.blade.php
if (file_exists($clientIndexPath)) {
    $content = file_get_contents($clientIndexPath);
    $layout = preg_match('/@extends\([\'"]([^\'"]+)[\'"]\)/', $content, $matches) ? $matches[1] : "No encontrado";
    echo "Layout usado en client/index.blade.php: " . $layout . "<br><br>";
}

// Verificar layouts
echo "Verificando layouts:<br>";
echo "-------------------<br>";

$masterLayoutPath = $viewsPath . '/layouts/master.blade.php';
$appLayoutPath = $viewsPath . '/layouts/app.blade.php';

echo "Layout layouts/master.blade.php: " . (file_exists($masterLayoutPath) ? "Existe" : "No existe") . "<br>";
echo "Layout layouts/app.blade.php: " . (file_exists($appLayoutPath) ? "Existe" : "No existe") . "<br><br>";

// Verificar rutas
echo "Verificando rutas:<br>";
echo "----------------<br>";

$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    
    // Buscar rutas clients.index
    $clientsIndexRoute = preg_match('/[\'"]clients.index[\'"]/', $routesContent) ? "Encontrada" : "No encontrada";
    echo "Ruta clients.index: " . $clientsIndexRoute . "<br>";
    
    // Buscar rutas client.index (antiguas)
    $clientIndexRoute = preg_match('/[\'"]client.index[\'"]/', $routesContent) ? "Encontrada" : "No encontrada";
    echo "Ruta client.index: " . $clientIndexRoute . "<br><br>";
}

// Verificar controlador
echo "Verificando controlador:<br>";
echo "----------------------<br>";

$controllerPath = __DIR__ . '/app/Http/Controllers/ClientController.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    
    // Buscar método index
    $indexMethod = preg_match('/public function index\(Request \$request\)/', $controllerContent) ? "Encontrado" : "No encontrado";
    echo "Método ClientController@index: " . $indexMethod . "<br>";
    
    // Buscar método indexOld
    $indexOldMethod = preg_match('/public function indexOld\(Request \$request\)/', $controllerContent) ? "Encontrado" : "No encontrado";
    echo "Método ClientController@indexOld: " . $indexOldMethod . "<br><br>";
    
    // Buscar vista que devuelve
    preg_match('/public function index\(Request \$request\)[^}]+return view\([\'"]([^\'"]+)[\'"]/', $controllerContent, $viewMatches);
    echo "Vista devuelta por ClientController@index: " . (isset($viewMatches[1]) ? $viewMatches[1] : "No encontrada") . "<br>";
    
    preg_match('/public function indexOld\(Request \$request\)[^}]+return view\([\'"]([^\'"]+)[\'"]/', $controllerContent, $viewOldMatches);
    echo "Vista devuelta por ClientController@indexOld: " . (isset($viewOldMatches[1]) ? $viewOldMatches[1] : "No encontrada") . "<br><br>";
}

// Verificar modelo Client
echo "Verificando modelo Client:<br>";
echo "------------------------<br>";

$clientModelPath = __DIR__ . '/app/Models/Client.php';
echo "Modelo Client: " . (file_exists($clientModelPath) ? "Existe" : "No existe") . "<br>";

if (file_exists($clientModelPath)) {
    $modelContent = file_get_contents($clientModelPath);
    $tableName = preg_match('/protected \$table = [\'"]([^\'"]+)[\'"]/', $modelContent, $tableMatches) ? $tableMatches[1] : "No especificada";
    echo "Tabla usada por el modelo Client: " . $tableName . "<br><br>";
}

// Verificar base de datos
echo "Verificando base de datos:<br>";
echo "------------------------<br>";

try {
    $dsn = 'mysql:host=127.0.0.1;dbname=sistema_prestamos;charset=utf8mb4';
    $username = 'root';
    $password = '';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Verificar tabla clients
    $stmt = $pdo->query("SHOW TABLES LIKE 'clients'");
    $clientsTableExists = $stmt->rowCount() > 0;
    echo "Tabla clients: " . ($clientsTableExists ? "Existe" : "No existe") . "<br>";
    
    if ($clientsTableExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM clients");
        $count = $stmt->fetch()['count'];
        echo "Número de registros en clients: " . $count . "<br>";
    }
    
    // Verificar tabla client_records
    $stmt = $pdo->query("SHOW TABLES LIKE 'client_records'");
    $recordsTableExists = $stmt->rowCount() > 0;
    echo "Tabla client_records: " . ($recordsTableExists ? "Existe" : "No existe") . "<br>";
    
    if ($recordsTableExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM client_records");
        $count = $stmt->fetch()['count'];
        echo "Número de registros en client_records: " . $count . "<br>";
    }
    
} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage() . "<br>";
}

echo "<br>Fin del diagnóstico"; 