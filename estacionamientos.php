<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estacionamiento Virtual</title>
    <style>
        body {
            background-color: #880E4F;
            font-family: Arial, sans-serif;
            color: white;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #B71C1C;
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
        }

        header img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        header h1 {
            font-size: 24px;
        }

        .bienvenida {
            text-align: center;
            margin: 20px;
        }

        .mascota {
            text-align: center;
        }

        .mascota img {
            width: 150px;
        }

        .saludo {
            color: #FBC02D;
            font-size: 18px;
            margin-top: 10px;
        }

        .entrada {
            color: white;
            width: 200px;
            text-align: center;
            padding: 10px;
            margin: 20px auto;
            font-weight: bold;
        }

        .estacionamiento {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            max-width: 600px;
            margin: 20px auto;
            padding: 0 15px;
        }

        .puesto {
            background-color: #ffffff22;
            border: 2px solid #ffffff33;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }

        .puesto:hover {
            background-color: #66bb6a;
        }

        .reservado {
            background-color: #c62828;
        }

        .salir {
            display: block;
            margin: 30px auto;
            padding: 10px 20px;
            background-color: #c62828;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 0 10px #b71c1c;
        }

        .salir:hover {
            background-color: #e53935;
            box-shadow: 0 0 15px #f44336;
        }

        footer {
            text-align: center;
            font-size: 14px;
            color: #ccc;
            margin-top: 40px;
            padding-bottom: 20px;
        }
        .foco-container {
    position: fixed;
    top: 20px;
    right: 20px;
    font-size: 35px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 1000;
}
.foco-container:hover .cuerda {
    transform: translateY(8px);
}

.cuerda {
    width: 3px;
    height: 30px;
    background: black;
    margin-top: 5px;
    transition: transform 0.3s;
}
    </style>
</head>
<body>
<div class="foco-container" onclick="alternarFondo()">
    💡
    <div class="cuerda"></div>
</div>

<header>
    <img src="UTA-LOGO.png" alt="Logo UTA">
    <h1>Estacionamiento Virtual</h1>
</header>

<div class="mascota">
    <img src="pumi.png" alt="Mascota UTA">
    <div class="saludo">¡Hola, bienvenido al sistema de parqueo UTA!</div>
</div>

<div class="bienvenida">Bienvenido a tu parqueadero universitario</div>

<div class="entrada">Elige tu puesto</div>

<div class="estacionamiento" id="estacionamiento"></div>

<!-- Botón de salida -->
<button class="salir" onclick="window.location.href='index.html'">SALIR</button>


<script>
    const contenedor = document.getElementById("estacionamiento");

    for (let i = 1; i <= 30; i++) {
        const puesto = document.createElement("div");
        puesto.classList.add("puesto");
        puesto.textContent = i;

        puesto.addEventListener("click", function () {
            if (!puesto.classList.contains("reservado")) {
                const confirmar = confirm("¿Reservar el puesto " + i + "?");
                if (confirmar) {
                    puesto.classList.add("reservado");
                    alert("¡Puesto " + i + " reservado!");
                }
            } else {
                alert("El puesto " + i + " ya está reservado.");
            }
        });

        contenedor.appendChild(puesto);
    }
   function alternarFondo() {
    const body = document.body;
    const esOscuro = body.style.backgroundColor === "rgb(136, 14, 79)";

    if (esOscuro) {
        body.style.backgroundColor = "#FFCDD2";  
        body.style.color = "black";               
    } else {
        body.style.backgroundColor = "#880E4F"; 
        body.style.color = "white";              
    }
} 
</script>

</body>
</html>
