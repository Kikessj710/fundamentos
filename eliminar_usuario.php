<?php
session_start();

// Verificar si el usuario es administrador
if ($_SESSION['rol'] !== 'administrador') {
    header("Location: index.html");  // Redirigir a login si no es administrador
    exit();
}

$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (isset($_POST['usuario_id'])) {
    $usuario_id = $_POST['usuario_id'];

    // Eliminar el usuario de la base de datos
    $query = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();

    echo "Usuario eliminado exitosamente.";
}
?>
