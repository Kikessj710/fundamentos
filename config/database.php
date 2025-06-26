<?php
// Definir constantes de configuración
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'mamiypapi1');
define('DB_NAME', 'login_proyecto');

// Crear conexión
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
