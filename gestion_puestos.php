<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está autenticado y si su rol es 'administrador'
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.html");  
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];
    $numero_puesto = $_POST['numero_puesto'];
    
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Error de conexión: " . $conn->connect_error);
        }

        switch ($accion) {
            case 'crear':
                // Verificar si el puesto ya existe
                $stmt = $conn->prepare("SELECT COUNT(*) FROM puestos WHERE numero = ?");
                $stmt->bind_param("s", $numero_puesto);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_row()[0];

                if ($count > 0) {
                    throw new Exception("El puesto ya existe");
                }

                // Insertar nuevo puesto con estado 'disponible'
                $stmt = $conn->prepare("INSERT INTO puestos (numero, estado) VALUES (?, 'disponible')");
                $stmt->bind_param("s", $numero_puesto);
                
                if ($stmt->execute()) {
                    $mensaje = "Puesto creado exitosamente";
                } else {
                    throw new Exception("Error al crear el puesto: " . $stmt->error);
                }
                break;

            case 'eliminar':
                // Verificar si el puesto existe
                $stmt = $conn->prepare("SELECT COUNT(*) FROM puestos WHERE numero = ?");
                $stmt->bind_param("s", $numero_puesto);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_row()[0];

                if ($count === 0) {
                    throw new Exception("El puesto no existe");
                }

                // Eliminar el puesto
                $stmt = $conn->prepare("DELETE FROM puestos WHERE numero = ?");
                $stmt->bind_param("s", $numero_puesto);
                
                if ($stmt->execute()) {
                    $mensaje = "Puesto eliminado exitosamente";
                } else {
                    throw new Exception("Error al eliminar el puesto: " . $stmt->error);
                }
                break;

            default:
                throw new Exception("Acción no válida");
        }

        $conn->close();
        
        // Redirigir de vuelta al dashboard con el mensaje
        header("Location: admin_dashboard.php?mensaje=" . urlencode($mensaje));
        exit();

    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
        header("Location: admin_dashboard.php?error=" . urlencode($error));
        exit();
    }
}
?>
