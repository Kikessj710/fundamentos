<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .formulario {
            background-color: #fff;
            color: #880E4F;
            width: 100%;
            max-width: 380px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .formulario:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .formulario h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #880E4F;
            font-weight: 600;
        }

        .formulario input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            outline: none;
        }

        .formulario input[type="submit"] {
            background-color: #B71C1C;
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .formulario input[type="submit"]:hover {
            background-color: #C62828;
        }

        .formulario .enlace {
            margin-top: 20px;
            font-size: 14px;
        }

        .formulario .enlace a {
            color: #B71C1C;
            text-decoration: none;
        }

        .formulario .enlace a:hover {
            text-decoration: underline;
        }

        .UTA-LOGO {
            width: 80px; /* Ajusta el tamaño del logo según tu necesidad */
            margin-bottom: 20px;
        }

        footer {
            position: fixed;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="formulario">
        <!-- Agregar el logo de la UTA en la parte superior -->
        <img src="UTA-LOGO.png" alt="Logo UTA" class="UTA-LOGO">

        <h1>Recuperar Contraseña</h1>
        <form method="post" action="enviar_codigo.php">
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="submit" value="Enviar Código">
        </form>
        <div class="enlace">
            <a href="index.html">Volver al inicio de sesión</a>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Universidad Técnica de Ambato | Sistema de Parqueo</p>
    </footer>
</body>
</html>
