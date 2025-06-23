<?php
session_start();
date_default_timezone_set('America/Guayaquil');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];
$reservas = [];

$stmt = $conexion->prepare("SELECT id, puesto, fecha, hora_inicio, hora_fin, accion FROM acciones_parqueo 
                            WHERE usuario_id = ? AND (accion = 'reservado' OR accion = 'cancelado')
                            ORDER BY fecha DESC, hora_inicio DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

while ($row = $resultado->fetch_assoc()) {
    $reservas[] = $row;
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
            color: #333;
        }
        .contenedor {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #00796B;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #00796B;
            color: white;
        }
        .cancelar-btn {
            background-color: #D32F2F;
            color: white;
            border: none;
            padding: 7px 15px;
            border-radius: 15px;
            cursor: pointer;
        }
        .cancelar-btn:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }
        .estado {
            font-weight: bold;
        }
        .estado.cancelado {
            color: #D32F2F;
        }
        .estado.no-disponible {
            color: gray;
        }
    </style>
</head>
<body>
<div class="contenedor">
    <h1>Mis Reservas</h1>
    <?php if (count($reservas) > 0): ?>
        <table>
            <tr>
                <th>Puesto</th>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($reservas as $reserva): 
                $fechaActual = date('Y-m-d');
                $horaActual = date('H:i');
                $fechaReserva = $reserva['fecha'];
                $horaInicio = $reserva['hora_inicio'];
                $puedeCancelar = false;
                $estado = ucfirst($reserva['accion']);

                if ($estado === 'Reservado') {
                    if ($fechaReserva > $fechaActual || ($fechaReserva === $fechaActual && $horaInicio > $horaActual)) {
                        $puedeCancelar = true;
                    } else {
                        $estado = 'No disponible';
                    }
                } elseif ($estado === 'Cancelado') {
                    $estado = 'Cancelado';
                }
            ?>
                <tr>
                    <td><?= $reserva['puesto'] ?></td>
                    <td><?= $fechaReserva ?></td>
                    <td><?= $horaInicio ?></td>
                    <td><?= $reserva['hora_fin'] ?></td>
                    <td class="estado <?= strtolower(str_replace(' ', '-', $estado)) ?>"><?= $estado ?></td>
                    <td>
                        <?php if ($puedeCancelar): ?>
                            <form method="POST" action="cancelar_reserva.php" onsubmit="return confirm('¿Seguro que desea cancelar esta reserva?');">
                                <input type="hidden" name="reserva_id" value="<?= $reserva['id'] ?>">
                                <button type="submit" class="cancelar-btn">Cancelar</button>
                            </form>
                        <?php elseif ($estado === 'No disponible'): ?>
                            <span class="estado no-disponible">No disponible</span>
                        <?php elseif ($estado === 'Cancelado'): ?>
                            <span class="estado cancelado">Cancelado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No tienes reservas registradas.</p>
    <?php endif; ?>
    <br>
    <a href="estacionamientos.php" style="text-decoration:none;color:#00796B;">← Volver al Estacionamiento</a>
</div>
</body>
</html>
