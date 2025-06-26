<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Datos del nuevo administrador
$username = 'admin';  // Nombre de usuario para el administrador
$password = 'ADMIN123';  // Contraseña en texto plano
$nombre = 'Administrador';  // Nombre del administrador
$telefono = '123456789';  // Teléfono del administrador
$rol = 'administrador';  // Rol (administrador)
$correo = 'admin@dominio.com';  // Correo del administrador

// Encriptar la contraseña con password_hash() (usando BCRYPT)
$password_encriptada = password_hash($password, PASSWORD_BCRYPT);

// Preparar la consulta SQL para insertar el administrador con la contraseña encriptada
$stmt = $conexion->prepare("INSERT INTO usuarios (username, password, nombre, telefono, rol, correo) 
                            VALUES (?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssss", $username, $password_encriptada, $nombre, $telefono, $rol, $correo);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Administrador creado exitosamente.";
    header("Location: success_page.php");  // Redirige a una página de éxito
} else {
    echo "Error al crear el administrador: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
