<?php 
$conexion = new mysqli("localhost", "root", "kikecrak710", "login_proyecto");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Recibir y validar datos del formulario
if(empty($_POST['usuario']) || empty($_POST['clave'])) {
    die("<script>alert('Por favor complete todos los campos'); window.history.back();</script>");
}

$usuario = $conexion->real_escape_string($_POST['usuario']);
$clave = $conexion->real_escape_string($_POST['clave']);

// Verificar si el usuario ya existe
$check = $conexion->prepare("SELECT id FROM usuarios WHERE usarname = ?");
$check->bind_param("s", $usuario);
$check->execute();
$check->store_result();
if($check->num_rows > 0) {
    die("<script>alert('El nombre de usuario ya está en uso'); window.history.back();</script>");
}
$check->close();

// Insertar nuevo usuario
$sql = "INSERT INTO usuarios (usarname, password) VALUES ('$usuario', '$clave')";

if ($conexion->query($sql)) {
    //Redirección con mensaje de éxito
    echo "<script>alert('Registro exitoso!'); window.location.href='http://localhost/proyecto_fundamentos/';</script>";
    exit();
} else {
    echo "<script>alert('Error al registrar: " . addslashes($conexion->error) . "'); window.history.back();</script>";
}

$conexion->close();
?>