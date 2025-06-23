<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Asegúrate de haber incluido Composer
require 'vendor/autoload.php';  // Si usas Composer

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");

if ($conexion->connect_error) {
    die("No tiene conexion: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];

    // Verificar si el correo existe en la base de datos
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo=?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Recuperar el nombre de usuario
        $usuario = $resultado->fetch_assoc();
        $nombre_usuario = $usuario['username'];  // Asumiendo que 'username' es el campo del nombre de usuario

        // Generar un token único
        $token = bin2hex(random_bytes(16));

        // Guardar el token en la base de datos con la fecha de expiración (por ejemplo, 1 hora)
        $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour'));  // Expira en 1 hora
        $stmt = $conexion->prepare("UPDATE usuarios SET token_recuperacion=?, token_expiracion=? WHERE correo=?");
        $stmt->bind_param("sss", $token, $expiracion, $correo);
        $stmt->execute();

        // Enviar el enlace de restablecimiento de contraseña
        $reset_link = "https://localhost/fundamentos_proyecto/restablecer.php?token=$token";  // Cambia la URL a la correcta
        $subject = "Recuperación de Contraseña - Universidad Técnica de Ambato";
        $message = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #fff;
                    color: #333;
                }
                .email-container {
                    background-color: #880E4F;
                    color: #fff;
                    width: 100%;
                    padding: 20px;
                    text-align: center;
                }
                .email-content {
                    background-color: #fff;
                    color: #880E4F;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                .email-body {
                    font-size: 16px;
                    margin-bottom: 20px;
                }
                .footer {
                    font-size: 14px;
                    color: #fff;
                    margin-top: 20px;
                }
                .reset-button {
                    background-color: #B71C1C;
                    color: white;
                    font-size: 18px;
                    padding: 12px 24px;
                    border-radius: 5px;
                    text-decoration: none;
                }
                .reset-button:hover {
                    background-color: #C62828;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-content'>
                    <div class='email-header'>
                        Recuperación de Contraseña
                    </div>
                    <div class='email-body'>
                        <p>Estimado/a $nombre_usuario,</p>
                        <p>Este es el enlace para restablecer tu contraseña para el sistema de parqueo de la <strong>Universidad Técnica de Ambato</strong>.</p>
                        <p><a href='$reset_link' class='reset-button'>Restablecer mi contraseña</a></p>
                        <p>Si no solicitaste este restablecimiento, por favor ignora este correo.</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; 2025 Universidad Técnica de Ambato | Sistema de Parqueo</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        // Configuración de PHPMailer para enviar el correo
        $mail = new PHPMailer(true);  // Instancia de PHPMailer
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp-mail.outlook.com';  // Servidor SMTP de Outlook
            $mail->SMTPAuth = true;
            $mail->Username = 'sfernandez7527@uta.edu.ec';  // Tu correo de Outlook
            $mail->Password = 'Mamiypapi1@';  // Tu contraseña de Outlook (o contraseña de aplicación si tienes 2FA)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;  // Puerto para TLS

            // Remitente y destinatario
            $mail->setFrom('sfernandez7527@uta.edu.ec', 'Sistema Estacionamiento');
            $mail->addAddress($correo);  // Correo del destinatario

            // Asunto y cuerpo del correo
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            // Enviar el correo
            $mail->send();
            echo "<script>alert('Se ha enviado un enlace de restablecimiento de contraseña a tu correo.'); window.location.href='index.html';</script>";
        } catch (Exception $e) {
            echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "<script>alert('No encontramos un usuario con ese correo.'); window.history.back();</script>";
    }

    $conexion->close();
}
?>
