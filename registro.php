<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de usuario</title>
    <link rel="stylesheet" href="style.css">  <!-- Puedes usar el mismo CSS -->
</head>
<body>
<div class="formulario">
    <h1>Registrarse</h1>
   <form method="post" action="guardar_usurio.php">

         <div class="nombre">
                <input type="text" name="nombre" placeholder="Nombre completo" required>
            </div>

            <div class="telefono">
                <input type="tel" name="telefono" placeholder="Teléfono">
            </div>

            <div class="carrera">
                <input type="text" name="carrera" placeholder="Carrera">
            </div>
        <div class="usarname">
            <input type="email" name="usuario" placeholder="Nombre de usuario" required>
        </div>
        <div class="password">
            <input type="password" name="clave" placeholder="Contraseña" required>
        </div>
        <input type="submit" value="registrarse">

            <div class="registrarse">
                ¿Ya tienes cuenta? <a href="index.html"> Inicia sesión </a>
            </div>

        </form>
    </div>

    <video autoplay muted loop id="video-fondo">
        <source src="uta2.mp4" type="video/mp4">
    </video>