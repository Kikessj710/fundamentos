<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está autenticado y si su rol es 'administrador'
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.html");  
    exit();
}

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener estadísticas del estacionamiento
$sql = "SELECT 
    COUNT(DISTINCT p.numero) as total_puestos,
    COUNT(DISTINCT CASE 
        WHEN a.accion = 'ocupar' THEN p.numero 
    END) as ocupados,
    COUNT(DISTINCT CASE 
        WHEN a.accion = 'reservar' THEN p.numero 
    END) as reservados,
    COUNT(DISTINCT p.numero) - 
    COUNT(DISTINCT CASE 
        WHEN a.accion IN ('ocupar', 'reservar') THEN p.numero 
    END) as disponibles
FROM puestos p
LEFT JOIN acciones_parqueo a ON p.numero = a.puesto 
AND DATE(a.fecha) = CURDATE()";

$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$estadisticas = $result->fetch_assoc();
if (!$estadisticas) {
    $estadisticas = [
        'total_puestos' => 0,
        'ocupados' => 0,
        'reservados' => 0,
        'disponibles' => 0
    ];
}

// Obtener las últimas 8 acciones (reservas/ocupaciones)
$result = $conn->query("SELECT a.*, u.nombre as usuario_nombre 
    FROM acciones_parqueo a 
    JOIN usuarios u ON a.usuario_id = u.id 
    WHERE a.accion IN ('reservar', 'ocupar')
    ORDER BY a.creado_en DESC 
    LIMIT 8");
$acciones_recientes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Estacionamiento UTA</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header {
            background: #f90000;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }

        .dashboard-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .section h3 {
            color: #f90000;
            margin-top: 0;
            border-bottom: 2px solid #f90000;
            padding-bottom: 10px;
        }

        .estadisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .estadistica-card {
            background: #f90000;
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .estadistica-card h4 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .estadistica-card .valor {
            font-size: 24px;
            font-weight: bold;
        }

        .acciones-recientes {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .acciones-recientes h4 {
            color: #f90000;
            margin: 0 0 15px 0;
        }

        .acciones-recientes table {
            width: 100%;
            border-collapse: collapse;
        }

        .acciones-recientes th,
        .acciones-recientes td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .acciones-recientes th {
            background: #f90000;
            color: white;
        }

        .acciones-recientes tr:hover {
            background: #f5f5f5;
        }

        .reservas-activas {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .reservas-activas h4 {
            color: #f90000;
            margin: 0 0 15px 0;
        }

        .reservas-activas table {
            width: 100%;
            border-collapse: collapse;
        }

        .reservas-activas th,
        .reservas-activas td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .reservas-activas th {
            background: #f90000;
            color: white;
        }

        .reservas-activas tr:hover {
            background: #f5f5f5;
        }

        .top-usuarios {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .top-usuarios h4 {
            color: #f90000;
            margin: 0 0 15px 0;
        }

        .top-usuarios table {
            width: 100%;
            border-collapse: collapse;
        }

        .top-usuarios th,
        .top-usuarios td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .top-usuarios th {
            background: #f90000;
            color: white;
        }

        .top-usuarios tr:hover {
            background: #f5f5f5;
        }

        form {
            display: grid;
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group {
            position: relative;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        select:focus {
            border-color: #f90000;
            outline: none;
        }

        .mensaje-validacion {
            color: #f90000;
            font-size: 14px;
            position: absolute;
            bottom: -20px;
            left: 0;
            display: none;
        }

        .form-group.invalid .mensaje-validacion {
            display: block;
        }

        button[type="submit"] {
            background: #f90000;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        button[type="submit"]:hover {
            background: #c30000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f90000;
            color: white;
        }

        tr:hover {
            background: #f5f5f5;
        }

        @media (max-width: 768px) {
            .dashboard {
                padding: 10px;
            }

            .section {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="dashboard-header">
            <h2>Panel de Administrador - Estacionamiento UTA</h2>
            <div class="user-info">
                <img src="https://via.placeholder.com/40" alt="Admin">
                <span>Administrador</span>
            </div>
        </div>

        <!-- Sección de Estadísticas -->
        <div class="section">
            <h3>Estado Actual del Estacionamiento</h3>
            <div class="estadisticas">
                <div class="estadistica-card">
                    <h4>Total de Puestos</h4>
                    <div class="valor"><?php echo $estadisticas['total_puestos']; ?></div>
                </div>
                <div class="estadistica-card">
                    <h4>Puestos Disponibles</h4>
                    <div class="valor"><?php echo $estadisticas['disponibles']; ?></div>
                </div>
                <div class="estadistica-card">
                    <h4>Puestos Reservados</h4>
                    <div class="valor"><?php echo $estadisticas['reservados']; ?></div>
                </div>
                <div class="estadistica-card">
                    <h4>Puestos Ocupados</h4>
                    <div class="valor"><?php echo $estadisticas['ocupados']; ?></div>
                </div>
            </div>
        </div>

        <!-- Últimas 8 Reservas/Ocupaciones -->
        <div class="acciones-recientes">
            <h4>Últimas 8 Reservas/Ocupaciones</h4>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Puesto</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acciones_recientes as $accion): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($accion['fecha'])); ?></td>
                        <td><?php echo htmlspecialchars($accion['usuario_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($accion['puesto']); ?></td>
                        <td><?php echo htmlspecialchars($accion['accion']); ?></td>
                        <td><?php echo htmlspecialchars($accion['estado']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Gestión de Usuarios -->
        <div class="section">
            <h3>Crear Usuario</h3>
             <form action="crear_usuario.php" method="POST" onsubmit="return validarFormulario()">
                <div class="form-group">
                    <label for="usuario">Nombre de usuario:</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>

                <div class="form-group">
                    <label for="clave">Contraseña:</label>
                    <input type="password" id="clave" name="clave" 
                           pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}"
                           title="Debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo especial."
                           required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" required>
                </div>

                <div class="form-group">
                    <label for="carrera">Carrera:</label>
                    <input type="text" id="carrera" name="carrera" required>
                </div>

                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="rol" required>
                        <option value="usuario">Usuario</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>
                <button type="submit">Crear usuario</button>
            </form>
        </div>

        <div class="section">
            <h3>Eliminar Usuario</h3>
            <form action="eliminar_usuario.php" method="POST">
                <div>
                    <label for="usuario_id">ID de usuario a eliminar:</label>
                    <input type="text" name="usuario_id" required>
                </div>
                <button type="submit">Eliminar usuario</button>
            </form>
        </div>

        <div class="section">
            <h3>Usuarios Registrados</h3>
            <?php
            // Conectar a la base de datos
            $conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

            if ($conexion->connect_error) {
                die("Error de conexión: " . $conexion->connect_error);
            }

            $query = "SELECT id, username, nombre, telefono, rol FROM usuarios";
            $result = $conexion->query($query);

            echo "<table>";
            echo "<tr><th>ID</th><th>Nombre de Usuario</th><th>Nombre</th><th>Teléfono</th><th>Rol</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['id']) . "</td>
                         <td>" . htmlspecialchars($row['username']) . "</td>
                         <td>" . htmlspecialchars($row['nombre']) . "</td>
                         <td>" . htmlspecialchars($row['telefono']) . "</td>
                         <td>" . htmlspecialchars($row['rol']) . "</td></tr>";
            }
            echo "</table>";
            ?>
        </div>

        <div class="section">
            <h3>Gestión de Puestos de Estacionamiento</h3>
            <form action="gestion_puestos.php" method="POST" id="formPuestos">
                <div class="form-group">
                    <label for="numero_puesto">Número de Puesto:</label>
                    <input type="text" id="numero_puesto" name="numero_puesto" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="accion" value="crear" class="boton-accion">Crear Puesto</button>
                    <button type="submit" name="accion" value="eliminar" class="boton-accion">Eliminar Puesto</button>
                </div>
            </form>

            <div class="puestos-lista">
                <h4>Puestos Existentes</h4>
                <div class="estacionamiento" id="estacionamientoAdmin">
                    <?php
                    // Consulta para mostrar los puestos existentes en formato de grid
                    $query_puestos = "SELECT numero, estado FROM puestos ORDER BY numero";
                    $result_puestos = mysqli_query($conn, $query_puestos);
                    
                    while($row = mysqli_fetch_assoc($result_puestos)) {
                        $clase_estado = $row['estado'];
                        echo "<div class='puesto $clase_estado' data-numero='" . htmlspecialchars($row['numero']) . "'>";
                        echo htmlspecialchars($row['numero']);
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>

        </div>

        <div class="section">
            <h3>Últimas 8 Reservas/Ocupaciones</h3>
            <?php
            $query = "SELECT puesto, accion, usuario_id, fecha, hora_inicio, hora_fin 
                      FROM acciones_parqueo 
                      ORDER BY creado_en DESC LIMIT 8";
            $result = $conexion->query($query);

            echo "<table>";
            echo "<tr><th>Puesto</th><th>Acción</th><th>Usuario ID</th><th>Fecha</th><th>Hora inicio</th><th>Hora fin</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['puesto']) . "</td>
                         <td>" . htmlspecialchars($row['accion']) . "</td>
                         <td>" . htmlspecialchars($row['usuario_id']) . "</td>
                         <td>" . htmlspecialchars($row['fecha']) . "</td>
                         <td>" . htmlspecialchars($row['hora_inicio']) . "</td>
                         <td>" . htmlspecialchars($row['hora_fin']) . "</td></tr>";
            }
            echo "</table>";
            ?>
        </div>
    </div>
</body>
</html>

    </div>
</body>
</html>

