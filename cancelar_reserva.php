<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reserva_id'])) {
    $reserva_id = $_POST['reserva_id'];
    $conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Actualiza el estado a 'cancelado'
    $stmt = $conexion->prepare("UPDATE acciones_parqueo SET accion = 'cancelado' WHERE id = ?");
    $stmt->bind_param("i", $reserva_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conexion->close();
        header("Location: reservas.php?mensaje=cancelado");
        exit();
    } else {
        echo "<script>alert('Error al cancelar la reserva'); window.history.back();</script>";
    }
}
?>
