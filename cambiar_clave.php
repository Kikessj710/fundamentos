<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Función para validar contraseña segura
function validar_contrasena_segura($clave) {
    return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $clave);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $actual    = isset($_POST['clave_actual']) ? trim($_POST['clave_actual']) : '';
    $nueva     = isset($_POST['clave_nueva']) ? trim($_POST['clave_nueva']) : '';
    $confirmar = isset($_POST['clave_confirmar']) ? trim($_POST['clave_confirmar']) : '';

    if ($actual === '' || $nueva === '' || $confirmar === '') {
        echo "<script>alert('Por favor, rellena todos los campos.'); window.history.back();</script>";
        exit();
    }

    if ($nueva !== $confirmar) {
        echo "<script>alert('La nueva contraseña y la confirmación no coinciden.'); window.history.back();</script>";
        exit();
    }

    // Validar fortaleza de la nueva contraseña
    if (!validar_contrasena_segura($nueva)) {
        echo "<script>alert('La nueva contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo especial.'); window.history.back();</script>";
        exit();
    }

    $usuario_id = $_SESSION['usuario_id'];

    // Verificar la contraseña actual
    $stmt = $conexion->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($clave_hash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($actual, $clave_hash)) {
        echo "<script>alert('La contraseña actual es incorrecta.'); window.history.back();</script>";
        exit();
    }

    // Encriptar y guardar nueva contraseña
    $nuevo_hash = password_hash($nueva, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_hash, $usuario_id);

    if ($stmt->execute()) {
        echo "<script>alert('Contraseña actualizada correctamente.'); window.location.href='perfil.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar la contraseña.'); window.history.back();</script>";
    }

    $stmt->close();
}

$conexion->close();
?>
