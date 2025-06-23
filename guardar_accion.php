<?php
session_start();

// Evitar que el navegador almacene esta página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo "No autorizado";
    exit();
}

$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$usuario_id  = $_SESSION['usuario_id'];
$puesto      = $_POST['puesto'];
$accion      = $_POST['accion'];
$fecha       = $_POST['fecha'] ?? date('Y-m-d');  // Usar la fecha proporcionada o la fecha actual
$hora_inicio = $_POST['hora_inicio'] ?? null;
$hora_fin    = $_POST['hora_fin'] ?? null;

// Si el usuario intenta liberar un puesto, primero verificamos si él fue quien lo ocupó
if ($accion === "liberado") {
    // Verifica si el usuario es el mismo que lo ocupó
    $verifica = $conexion->prepare("SELECT usuario_id FROM acciones_parqueo 
        WHERE puesto = ? AND accion = 'ocupado' 
        AND DATE(fecha) = CURDATE() 
        ORDER BY creado_en DESC LIMIT 1");
    $verifica->bind_param("i", $puesto);
    $verifica->execute();
    $verifica->bind_result($usuario_ocupante);
    $verifica->fetch();
    $verifica->close();

    // Si el usuario no es el que ocupó el puesto, no puede liberarlo
    if ($usuario_ocupante !== $usuario_id) {
        http_response_code(403);
        echo "No puedes liberar este puesto porque no lo ocupaste tú.";
        $conexion->close();
        exit();
    }
}

// Verificar si el puesto ya está reservado o ocupado antes de registrar
if ($accion === "reservado" || $accion === "ocupado") {
    // Verificar el estado actual del puesto para hoy o para una fecha futura
    $verificaEstado = $conexion->prepare("SELECT accion FROM acciones_parqueo WHERE puesto = ? AND DATE(fecha) = ? ORDER BY creado_en DESC LIMIT 1");
    $verificaEstado->bind_param("is", $puesto, $fecha); // Usar la fecha proporcionada por el usuario
    $verificaEstado->execute();
    $verificaEstado->bind_result($estado);
    $verificaEstado->fetch();
    $verificaEstado->close();

    // Si el puesto ya está ocupado o reservado para esa fecha, no permitir la acción
    if ($estado === 'ocupado' || $estado === 'reservado') {
        http_response_code(403);
        echo "Este puesto ya está ocupado o reservado para la fecha seleccionada.";
        $conexion->close();
        exit();
    }
}

// Registrar la acción (reservar o ocupar)
$stmt = $conexion->prepare("INSERT INTO acciones_parqueo (usuario_id, puesto, accion, fecha, hora_inicio, hora_fin) 
                            VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $usuario_id, $puesto, $accion, $fecha, $hora_inicio, $hora_fin);
$stmt->execute();

// Verificar si la acción se ha registrado correctamente
if ($stmt->affected_rows > 0) {
    echo "Acción registrada";
} else {
    echo "Error al guardar";
}

$stmt->close();
$conexion->close();
?>
