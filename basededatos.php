<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "kikecrak710", "login_proyecto");

if ($conexion->connect_error) {
    die("No tiene conexion: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibimos los datos del formulario
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Consulta preparada para evitar inyecciones SQL
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usarname=? AND password=?");
    $stmt->bind_param("ss", $usuario, $clave); 
    $stmt->execute(); 

  
    $resultado = $stmt->get_result();

    // Verificar si hay filas coincidentes (usuario y contraseña correctos)
    if ($resultado->num_rows > 0) {
         header("Location: estacionamientos.php");
        exit();

    }else{
        echo "<script>alert('Usuario o contraseña incorrectos.'); window.history.back();</script>";
    }

    // Cerrar la consulta y la conexión
    $stmt->close();
    $conexion->close();
}
?>


