<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("No tiene conexión: " . $conexion->connect_error);
}

// Verificar si el token está en la URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar si el token existe y no ha expirado
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE token_recuperacion=? AND token_expiracion > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nueva     = isset($_POST['nueva_contrasena']) ? trim($_POST['nueva_contrasena']) : '';
            $confirmar = isset($_POST['confirmar_contrasena']) ? trim($_POST['confirmar_contrasena']) : '';

            if (empty($nueva) || empty($confirmar)) {
                echo "<script>alert('Por favor, completa todos los campos.');</script>";
            } elseif ($nueva !== $confirmar) {
                echo "<script>alert('Las contraseñas no coinciden.');</script>";
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $nueva)) {
                echo "<script>alert('La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.');</script>";
            } else {
                $clave_hash = password_hash($nueva, PASSWORD_DEFAULT);
                $stmt = $conexion->prepare("UPDATE usuarios SET password=?, token_recuperacion=NULL, token_expiracion=NULL WHERE token_recuperacion=?");
                $stmt->bind_param("ss", $clave_hash, $token);
                $stmt->execute();

                echo "<script>alert('Contraseña actualizada correctamente.'); window.location.href='index.html';</script>";
                exit();
            }
        }
    } else {
        echo "<script>alert('El token es inválido o ha expirado.'); window.location.href='index.html';</script>";
        exit();
    }
} else {
    echo "<script>alert('Token no proporcionado.'); window.location.href='index.html';</script>";
    exit();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #880E4F;
            color: #fff;
            width: 100%;
            padding: 40px;
        }
        .form-container {
            background-color: #fff;
            color: #880E4F;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin: 0 auto;
        }
        .input-field {
            margin: 10px 0;
            padding: 10px;
            width: 80%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .submit-button {
            background-color: #B71C1C;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-button:hover {
            background-color: #C62828;
        }
        .info {
            font-size: 0.9em;
            margin-top: 10px;
            color: #444;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Restablecer Contraseña</h2>
        <form method="POST">
            <input type="password" name="nueva_contrasena" class="input-field" placeholder="Nueva Contraseña" required
                   pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}"
                   title="Debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.">

            <input type="password" name="confirmar_contrasena" class="input-field" placeholder="Confirmar Contraseña" required>

            <p class="info">La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.</p>
            <input type="submit" value="Restablecer" class="submit-button">
        </form>
    </div>
</div>

</body>
</html>
