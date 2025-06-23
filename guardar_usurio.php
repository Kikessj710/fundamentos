<?php 
$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (
    empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['correo']) ||
    empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['carrera'])
) {
    die("<script>alert('Por favor complete todos los campos'); window.history.back();</script>");
}

$usuario  = $conexion->real_escape_string($_POST['usuario']);
$clave    = $conexion->real_escape_string($_POST['clave']);
$correo   = $conexion->real_escape_string($_POST['correo']);
$nombre   = $conexion->real_escape_string($_POST['nombre']);
$telefono = $conexion->real_escape_string($_POST['telefono']);
$carrera  = $conexion->real_escape_string($_POST['carrera']);

// ✅ Validar seguridad de la contraseña
$patron_contraseña_segura = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/';
if (!preg_match($patron_contraseña_segura, $clave)) {
    die("<script>alert('La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo especial.'); window.history.back();</script>");
}

// Verificar si el nombre de usuario ya existe
$check = $conexion->prepare("SELECT id FROM usuarios WHERE username = ?");
$check->bind_param("s", $usuario);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    die("<script>alert('El nombre de usuario ya está en uso'); window.history.back();</script>");
}
$check->close();

// Verificar si el correo ya existe
$checkCorreo = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
$checkCorreo->bind_param("s", $correo);
$checkCorreo->execute();
$checkCorreo->store_result();
if ($checkCorreo->num_rows > 0) {
    die("<script>alert('Ya existe una cuenta registrada con ese correo'); window.history.back();</script>");
}
$checkCorreo->close();

// Hashear la clave antes de guardarla
$clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$sql = "INSERT INTO usuarios (username, password, nombre, telefono, carrera, correo) 
        VALUES ('$usuario', '$clave_encriptada', '$nombre', '$telefono', '$carrera', '$correo')";

if ($conexion->query($sql)) {
    echo "<script>alert('Registro exitoso.'); window.location.href='http://localhost/fundamentos_proyecto/index.html';</script>";
    
    // Enviar código por correo si usas PHPMailer
    $codigo = rand(100000, 999999);
    require 'vendor/autoload.php';
    if (enviarCodigo($correo, $codigo)) {
        echo "<script>alert('Código enviado al correo.');</script>";
    } else {
        echo "<script>alert('Error al enviar el código.');</script>";
    }
    exit();
} else {
    echo "<script>alert('Error al registrar: " . addslashes($conexion->error) . "'); window.history.back();</script>";
}

$conexion->close();
?>
