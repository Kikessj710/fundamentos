<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de usuario</title>
    <link rel="stylesheet" href="style.css">
    <style>
        small {
            font-size: 12px;
            color: #555;
        }
        .mensaje-validacion {
            font-size: 12px;
            color: #D32F2F;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
<div class="formulario">
    <h1>Registrarse</h1>
    <form method="post" action="guardar_usurio.php" onsubmit="return validarFormulario()">

        <div class="nombre">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
        </div>

        <div class="telefono">
            <input type="tel" name="telefono" placeholder="Teléfono" required>
        </div>

        <div class="carrera">
            <input type="text" name="carrera" placeholder="Carrera" required>
        </div>

        <div class="usarname">
            <input type="text" name="usuario" placeholder="Nombre de usuario" required>
        </div>

        <div class="password">
            <input type="password" name="clave" id="clave" placeholder="Contraseña" required
                pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}"
                title="Debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo especial.">
            
            <div class="mensaje-validacion" id="mensajeClave">La contraseña no cumple con los requisitos.</div>
        </div>

        <input type="email" name="correo" placeholder="Correo electrónico" required><br>

        <input type="submit" value="Registrarse">

        <div class="registrarse">
            ¿Ya tienes cuenta? <a href="index.html">Inicia sesión</a>
        </div>
    </form>
</div>

<video autoplay muted loop id="video-fondo">
    <source src="uta2.mp4" type="video/mp4">
</video>

<script>
function validarFormulario() {
    const clave = document.getElementById("clave").value;
    const mensaje = document.getElementById("mensajeClave");
    const regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (!regex.test(clave)) {
        mensaje.style.display = "block";
        return false;
    }

    mensaje.style.display = "none";
    return true;
}
</script>
</body>
</html>
