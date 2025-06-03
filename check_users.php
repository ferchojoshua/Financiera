<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema_prestamos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

echo "Conexión exitosa a la base de datos.<br>";

// Consultar usuarios
$sql = "SELECT id, name, last_name, email, username, level, role FROM users";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        echo "Usuarios encontrados: " . $result->num_rows . "<br>";
        echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th><th>Usuario</th><th>Nivel</th><th>Rol</th></tr>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["last_name"]."</td><td>".$row["email"]."</td><td>".$row["username"]."</td><td>".$row["level"]."</td><td>".$row["role"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron usuarios.<br>";
    }
} else {
    echo "Error al consultar usuarios: " . $conn->error . "<br>";
}

// Verificar la existencia de la tabla users
$sql = "SHOW TABLES LIKE 'users'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "La tabla 'users' existe.<br>";
} else {
    echo "La tabla 'users' no existe.<br>";
}

// Verificar tablas disponibles
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Tablas disponibles:<br><ul>";
    while($row = $result->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No hay tablas en la base de datos.<br>";
}

$conn->close();
?> 