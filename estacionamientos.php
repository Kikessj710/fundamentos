<?php
session_start();

// Evita que el navegador almacene esta página
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Validar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}
$conexion = new mysqli("localhost", "root", "mamiypapi1", "login_proyecto");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$puestos_estado = [];
$result = $conexion->query("SELECT puesto, accion, usuario_id FROM acciones_parqueo WHERE DATE(fecha) = CURDATE() ORDER BY creado_en DESC");
while ($row = $result->fetch_assoc()) {
    $puesto = $row['puesto'];
    if (!isset($puestos_estado[$puesto])) {
        $puestos_estado[$puesto] = [
            'accion' => $row['accion'],
            'usuario_id' => $row['usuario_id']
        ];
    }
}

$conexion->close();

?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estacionamiento Virtual</title>
    <style>
        /* Estilos globales */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ffffff;
            color: #333333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
            flex-direction: column;
        }
        /* Barra roja superior */
        .barra-roja {
            width: 100%;
            height: 20px;
            background-color: #D32F2F;
            position: absolute;
            top: 0;
        }
        /* Header */
        header {
            text-align: center;
            padding: 20px;
            background-color: #00796B;
            color: white;
            width: 100%;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }
        header img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            vertical-align: middle;
        }
        header h1 {
            display: inline-block;
            font-size: 24px;
            margin: 0;
            vertical-align: middle;
        }
        /* Mensaje principal */
        .mensaje-estacionamiento {
            margin-top: 50px;
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border: 3px solid #D32F2F;
            width: 100%;
            max-width: 900px;
            margin-bottom: 30px;
            font-size: 22px;
            color: #00796B;
        }
        /* Contenedor principal */
        .contenedor {
            width: 100%;
            max-width: 900px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
            margin-top: 60px;
            text-align: center;
        }
        .bienvenida {
            font-size: 20px;
            color: #00796B;
            margin-bottom: 30px;
        }
        .estacionamiento {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            justify-items: center;
            width: 100%;
            margin-bottom: 20px;
        }
        .puesto {
            background-color: #ffffff;
            border: 2px solid #00796B;
            height: 100px;
            width: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            box-sizing: border-box;
        }
        .puesto:hover {
            background-color: #00796B;
            color: white;
            transform: scale(1.05);
        }
        .disponible { background-color: #66bb6a; }
        .reservado   { background-color: #ff9800; }
        .ocupado     { background-color: #e53935; }
        /* Panel lateral */
        .panel-lateral {
            position: fixed;
            right: 0; top: 0;
            width: 300px; height: 100%;
            background-color: #ffffff;
            padding: 20px;
            display: none;
            box-shadow: -3px 0px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            z-index: 10;
        }
        .panel-lateral h2 { margin-top: 0; }
        .boton-accion {
            padding: 10px;
            margin-top: 20px;
            background-color: #00796B;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }
        .boton-accion:hover { background-color: #004d40; }
        .boton-cerrar-panel {
            padding: 10px;
            margin-top: 20px;
            background-color: #c62828;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        /* Formulario de reserva */
        .panel-lateral form {
            display: flex;
            flex-direction: column;
        }
        .panel-lateral form label {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .panel-lateral form input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: auto;
        }
        /* Modal y overlay */
        .modal {
            display: none;
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            z-index: 1000;
            width: 300px;
            text-align: center;
        }
        .modal.active   { display: block; }
        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            z-index: 999;
        }
        .overlay.active { display: block; }
        /* Imagenes laterales */
        .lateral {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 150px;
            text-align: center;
            color: #00796B;
            font-size: 14px;
        }
        .lateral img {
            width: 100%; border-radius: 50%;
            margin-bottom: 20px;
        }
        .lateral p {
            font-size: 14px;
            line-height: 1.5;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 10px;
        }
        /* Recomendaciones abajo */
        .datos-curiosos-izquierda {
            bottom: 100px; left: 10px; width: 200px; position: absolute;
        }
        .datos-curiosos-derecha {
            bottom: 100px; right: 10px; width: 200px; position: absolute;
        }
        .datos-curiosos {
            position: absolute; bottom: 10px; left: 10px; right: 10px;
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        /* Botón Mi Perfil */
        .usuario-menu {
            position: absolute;
            top: 10px; right: 10px;
            font-size: 16px;
            color: white;
            background-color: #00796B;
            padding: 10px;
            border-radius: 20px;
            cursor: pointer;
            z-index: 11;
        }
        .usuario-dropdown {
            display: none;
            position: absolute;
            top: 40px; right: 10px;
            background-color: white;
            border: 1px solid #00796B;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-size: 14px;
            z-index: 11;
        }
        .usuario-dropdown a {
            display: block;
            margin: 5px 0;
            color: #00796B;
            text-decoration: none;
        }
        .usuario-dropdown a:hover {
            background-color: #f1f1f1;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="barra-roja"></div>
    <header>
        <img src="UTA-LOGO.png" alt="Logo UTA">
        <h1>Estacionamiento Virtual</h1>
    </header>

    <div class="mensaje-estacionamiento">
        Estacionamiento Virtual UTA
    </div>

    <div class="contenedor">
        <div class="bienvenida">
    <?php echo "Bienvenido, " . htmlspecialchars($_SESSION['username']) . " al sistema de parqueo UTA"; ?>
</div>

        <div class="estacionamiento" id="estacionamiento"></div>

        <!-- Panel lateral -->
        <div class="panel-lateral" id="panelLateral">
            <div id="opcionesPanel">
                <h2>¿Qué deseas hacer?</h2>
                <p id="infoPuesto"></p>
                <button class="boton-accion" id="reservarBtn">Reservar</button>
                <button class="boton-accion" id="ocuparBtn">Ocupar</button>
                <button class="boton-cerrar-panel" onclick="cerrarPanel()">Cerrar</button>
            </div>
            <div id="reservaPanel" style="display:none;">
                <h2>Reserva puesto <span id="reservaNumero"></span></h2>
                <form id="formReserva">
                    <label>Fecha:
                        <input type="date" id="reservaFecha" required>
                    </label>
                    <label>Hora inicio:
                        <input type="time" id="horaInicio" required>
                    </label>
                    <label>Hora fin:
                        <input type="time" id="horaFin" required>
                    </label>
                    <button type="button" class="boton-accion" id="confirmarReservaBtn">Confirmar reserva</button>
                    <button type="button" class="boton-cerrar-panel" id="cancelarReservaBtn">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- Modal ocupado -->
        <div class="overlay" id="overlay"></div>
        <div class="modal" id="modal">
            <h2>Lo siento</h2>
            <p>Este espacio está reservado. Inténtalo más tarde.</p>
            <button onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>

    <!-- Botón Mi Perfil -->
    <div class="usuario-menu" onclick="toggleUsuarioMenu()">Mi Perfil</div>
    <div class="usuario-dropdown" id="usuarioDropdown">
        <a href="perfil.php">Mi perfil</a>
        <a href="reservas.php">Mis reservas</a>
        <a href="#" onclick="cerrarSesion()">Cerrar sesión</a>
    </div>
<script>
    const puestosEstado = <?= json_encode($puestos_estado) ?>;
    const usuarioActualId = <?= json_encode($_SESSION['usuario_id']) ?>;
</script>
<script>
    const usuarioActualId = <?= json_encode($_SESSION['usuario_id']) ?>;
    puestoSeleccionado.dataset.usuarioId = usuarioActualId;

</script>

    <!-- Imágenes y mensajes laterales -->
    
    <div class="lateral" style="right: 10px;">
        <img src="pumi.png" alt="Pumi Mascota">
        <p>Respetar las normas de estacionamiento es importante para mantener el orden y la seguridad.</p>
    </div>s

    <!-- Recomendaciones abajo -->
    <div class="datos-curiosos-izquierda">
        <div class="datos-curiosos">
            <h3>Curiosidades de la UTA</h3>
            <p>La UTA tiene más de 50 años de historia formando profesionales de calidad para el país.</p>
            <p>Es una de las universidades más prestigiosas de Ecuador, especialmente conocida por su enfoque en ingeniería y ciencias aplicadas.</p>
        </div>
    </div>
    <div class="datos-curiosos-derecha">
        <div class="datos-curiosos">
            <h3>Curiosidades de la UTA</h3>
            <p>La UTA se encuentra entre las principales universidades de Ecuador, con gran énfasis en la investigación y el desarrollo tecnológico.</p>
            <p>Cuenta con una amplia oferta académica y programas de intercambio con universidades internacionales.</p>
        </div>
    </div>

    <script>
        // Menú usuario
        function toggleUsuarioMenu() {
            const menu = document.getElementById("usuarioDropdown");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }
        function cerrarSesion() {
    fetch("cerrar_sesion.php")
        .then(() => {
            window.location.href = "index.html";
        });
}


        // Referencias DOM
        const contenedor = document.getElementById("estacionamiento");
        const panelLateral = document.getElementById("panelLateral");
        const opcionesPanel = document.getElementById("opcionesPanel");
        const reservaPanel = document.getElementById("reservaPanel");
        const infoPuesto = document.getElementById("infoPuesto");
        const reservarBtn = document.getElementById("reservarBtn");
        const ocuparBtn = document.getElementById("ocuparBtn");
        const confirmarReservaBtn = document.getElementById("confirmarReservaBtn");
        const cancelarReservaBtn = document.getElementById("cancelarReservaBtn");
        const reservaNumero = document.getElementById("reservaNumero");
        const overlay = document.getElementById("overlay");
        const modal = document.getElementById("modal");
        let puestoSeleccionado;

        // Crear puestos
       for (let i = 1; i <= 30; i++) {
    const puesto = document.createElement("div");
    puesto.classList.add("puesto");

   if (puestosEstado[i]) {
    const estado = puestosEstado[i];
    if (estado.accion === 'ocupado') {
        puesto.classList.add("ocupado");
        puesto.dataset.usuarioId = estado.usuario_id;
        puesto.dataset.startTime = Date.now(); // OJO: opcionalmente puedes guardar la hora real
    } else if (estado.accion === 'reservado') {
        puesto.classList.add("reservado");
        puesto.dataset.usuarioId = estado.usuario_id;
    } else {
        puesto.classList.add("disponible");
    }
} else {
    puesto.classList.add("disponible");
}


    puesto.textContent = i;
    // ... el resto queda igual

            puesto.textContent = i;
            puesto.addEventListener("click", () => {
                if (puesto.classList.contains("disponible")) {
                    puestoSeleccionado = puesto;
                    infoPuesto.textContent = `¿Deseas ocupar o reservar el puesto ${i}?`;
                    opcionesPanel.style.display = "block";
                    reservaPanel.style.display  = "none";
                    panelLateral.style.display   = "block";
                } else if (puesto.classList.contains("reservado")) {
                    if (confirm("El puesto está reservado. ¿Quieres liberar este puesto?")) {
                       puesto.classList.replace("reservado", "disponible");
fetch('guardar_accion.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `puesto=${puesto.textContent}&accion=liberado`
}).then(res => res.text()).then(msg => {
    alert(`¡Puesto liberado exitosamente!\n${msg}`);
});

                        cerrarPanel();
                    }
                } else {
    // Ocupado: calcular tiempo y preguntar
    const start = parseInt(puesto.dataset.startTime, 10);
    const diff = Date.now() - start;
    const hrs = Math.floor(diff / 3600000);
    const mins = Math.floor((diff % 3600000) / 60000);
    const secs = Math.floor((diff % 60000) / 1000);
const ocupante = puesto.dataset.usuarioId;
if (ocupante && ocupante != usuarioActualId) {
    alert("Este puesto no está disponible por el momento");
    return;
}

    if (confirm(`Este puesto estuvo ocupado por ${hrs}h ${mins}m ${secs}s.\n¿Deseas liberarlo?`)) {
        puesto.classList.replace("ocupado", "disponible");
        delete puesto.dataset.startTime;

        const ahora = new Date();
        const fecha = ahora.toISOString().split('T')[0];               // yyyy-mm-dd
        const hora  = ahora.toTimeString().split(' ')[0].slice(0, 5);  // hh:mm

        fetch('guardar_accion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `puesto=${puesto.textContent}&accion=liberado&fecha=${fecha}&hora_inicio=${hora}&hora_fin=${hora}`
        }).then(res => res.text()).then(msg => {
            alert(`¡Puesto liberado exitosamente!\n${msg}`);
        });
    }
}

            });
            contenedor.appendChild(puesto);
        }

        // Reservar → mostrar formulario
        reservarBtn.addEventListener("click", () => {
            reservaNumero.textContent = puestoSeleccionado.textContent;
            opcionesPanel.style.display = "none";
            reservaPanel.style.display  = "block";
        });

        // Confirmar reserva
        confirmarReservaBtn.addEventListener("click", () => {
            const fecha  = document.getElementById("reservaFecha").value;
            const inicio = document.getElementById("horaInicio").value;
            const fin    = document.getElementById("horaFin").value;
            if (!fecha || !inicio || !fin) {
                return alert("Completa fecha, hora inicio y hora fin.");
            }
            puestoSeleccionado.classList.replace("disponible", "reservado");
            puestoSeleccionado.dataset.reserva = `${fecha} ${inicio}-${fin}`;
           fetch('guardar_accion.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `puesto=${puestoSeleccionado.textContent}&accion=reservado&fecha=${fecha}&hora_inicio=${inicio}&hora_fin=${fin}`
}).then(res => res.text()).then(msg => {
    alert(`¡Puesto ${puestoSeleccionado.textContent} reservado!\nFecha: ${fecha}\nDesde: ${inicio}\nHasta: ${fin}\n${msg}`);
});

            cerrarPanel();
        });

        // Cancelar reserva
        cancelarReservaBtn.addEventListener("click", cerrarPanel);

        // Ocupar → marcar y guardar timestamp
        ocuparBtn.addEventListener("click", () => {
            puestoSeleccionado.classList.replace("disponible", "ocupado");
            puestoSeleccionado.dataset.startTime = Date.now();
const ahora = new Date();
const fecha = ahora.toISOString().split('T')[0];
const hora = ahora.toTimeString().split(' ')[0].slice(0, 5);

fetch('guardar_accion.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `puesto=${puestoSeleccionado.textContent}&accion=ocupado&fecha=${fecha}&hora_inicio=${hora}`
}).then(res => res.text()).then(msg => {
    alert(`¡Puesto ${puestoSeleccionado.textContent} ocupado exitosamente!\n${msg}`);
});
            cerrarPanel();
        });

        // Cerrar panel y reset vistas
        function cerrarPanel() {
            panelLateral.style.display  = "none";
            reservaPanel.style.display  = "none";
            opcionesPanel.style.display = "block";
        }

        // Cerrar modal (overlay no usado aquí)
        function cerrarModal() {
            overlay.classList.remove("active");
            modal.classList.remove("active");
        }
    </script>
    <script>
window.addEventListener("pageshow", function (event) {
    if (event.persisted || performance.getEntriesByType("navigation")[0].type === "back_forward") {
        window.location.reload();
    }
});
</script>


</body>
</html>
