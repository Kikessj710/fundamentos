<?php

session_start();    
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("No tiene conexión: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibimos los datos del formulario
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Consulta preparada para evitar inyecciones SQL
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username=?");
    $stmt->bind_param("s", $usuario);  // Enlazamos el parámetro con la variable
    $stmt->execute(); 

    $resultado = $stmt->get_result();

    // Verificar si el usuario existe
    if ($resultado->num_rows > 0) {
        // Recuperar el usuario de la base de datos
        $usuario_bd = $resultado->fetch_assoc();
        
        // Verificar la contraseña usando password_verify (para contraseñas encriptadas)
        if (password_verify($clave, $usuario_bd['password'])) {
            // Contraseña correcta, almacenar en la sesión
            $_SESSION['usuario_id'] = $usuario_bd['id'];
            $_SESSION['username'] = $usuario_bd['username'];
            $_SESSION['rol'] = $usuario_bd['rol'];  // Guardar el rol del usuario (usuario o administrador)
            
            // Redirigir según el rol del usuario
            if ($_SESSION['rol'] === 'administrador') {
                header("Location: admin_dashboard.php");  // Redirigir al panel de administrador
            } else {
                header("Location: estacionamientos.php");  // Redirigir al panel de usuario normal
            }
            exit();
        } else {
            // Contraseña incorrecta
            echo "<script>alert('Usuario o contraseña incorrectos.'); window.history.back();</script>";
        }
    } else {
        // Usuario no encontrado
        echo "<script>alert('Usuario o contraseña incorrectos.'); window.history.back();</script>";
    }

    // Cerrar la consulta y la conexión
    $stmt->close();
    $conexion->close();
}
?>
