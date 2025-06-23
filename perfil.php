<?php
session_start();
$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener datos personales
$stmt = $conexion->prepare("SELECT username, nombre, telefono, correo, carrera, fecha_registro FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($username, $nombre, $telefono, $correo, $carrera, $fecha_registro);
$stmt->fetch();
$stmt->close();

// Obtener historial de acciones (últimos 10)
$acciones = [];
$stmt2 = $conexion->prepare("SELECT puesto, accion, fecha, hora_inicio, hora_fin 
                             FROM acciones_parqueo 
                             WHERE usuario_id = ? AND accion != 'reservado' 
                             ORDER BY creado_en DESC LIMIT 10");
$stmt2->bind_param("i", $usuario_id);
$stmt2->execute();
$result = $stmt2->get_result();
while ($row = $result->fetch_assoc()) {
    $acciones[] = $row;
}
$stmt2->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            padding: 40px;
            color: #333;
        }
        .contenedor-perfil {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #00796B;
            margin-bottom: 20px;
        }
        .perfil-info, .acciones-historial, .cambiar-clave {
            margin-bottom: 30px;
        }
        .perfil-info p {
            margin: 8px 0;
        }
        .acciones-historial table {
            width: 100%;
            border-collapse: collapse;
        }
        .acciones-historial th, .acciones-historial td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .acciones-historial th {
            background-color: #00796B;
            color: white;
        }
        .btn-volver {
            display: inline-block;
            background-color: #D32F2F;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 20px;
        }
        .form-cambiar-clave input {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-cambiar-clave button {
            background-color: #00796B;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="contenedor-perfil">
        <h1>Mi Perfil</h1>

        <div class="perfil-info">
            <h2>Información Personal</h2>
            <p><strong>Usuario:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($telefono) ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($correo) ?></p>
            <p><strong>Carrera:</strong> <?= htmlspecialchars($carrera) ?></p>
            <p><strong>Registrado desde:</strong> <?= $fecha_registro ?></p>
        </div>

        <div class="acciones-historial">
            <h2>Últimas Acciones de Parqueo</h2>
            <?php if (count($acciones) > 0): ?>
                <table>
                    <tr>
                        <th>Puesto</th>
                        <th>Acción</th>
                        <th>Fecha</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                    </tr>
                    <?php foreach ($acciones as $accion): ?>
                        <tr>
                            <td><?= $accion['puesto'] ?></td>
                            <td><?= ucfirst($accion['accion']) ?></td>
                            <td><?= $accion['fecha'] ?></td>
                            <td><?= $accion['hora_inicio'] ?></td>
                            <td><?= $accion['hora_fin'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No se han registrado acciones aún.</p>
            <?php endif; ?>
        </div>

        <div class="cambiar-clave">
            <h2>Cambiar Contraseña</h2>
            <form class="form-cambiar-clave" method="POST" action="cambiar_clave.php">
                <label>Contraseña actual:</label>
                <input type="password" name="clave_actual" required>
                <label>Nueva contraseña:</label>
               <input type="password" name="clave_nueva" required
       pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}"
       title="Debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo especial.">
<small style="color:gray;">Debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.</small>

                <input type="password" name="clave_confirmar" required>
                <button type="submit">Actualizar Contraseña</button>
            </form>
        </div>

        <a class="btn-volver" href="estacionamientos.php">← Volver al Estacionamiento</a>
    </div>
</body>
</html>
