<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'];
    $nueva_contrasena = $_POST['nueva_contrasena'];

    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");
    if ($conexion->connect_error) {
        die("No tiene conexion: " . $conexion->connect_error);
    }

    // Verificar si el código ingresado es correcto
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE codigo_recuperacion=?");
    $stmt->bind_param("i", $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Actualizar la contraseña
        $stmt = $conexion->prepare("UPDATE usuarios SET password=? WHERE codigo_recuperacion=?");
        $stmt->bind_param("si", $nueva_contrasena, $codigo);
        $stmt->execute();
        
        echo "<script>alert('Contraseña actualizada correctamente.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Código incorrecto.'); window.history.back();</script>";
    }

    $conexion->close();
}
?>
