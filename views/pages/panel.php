<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Estaciones</title>
    <style>
        * {
            padding: 0em;
            margin: 0em;
            box-sizing: border-box;
            font-family: 'Ubuntu', sans-serif;
        }

        body { 
            /* El degradado exacto de la pantalla del profesor */
            background: linear-gradient(180deg, #4b527e 0%, #37306b 40%, #b84a39 100%);
            background-attachment: fixed;
            color: #fff; 
            padding: 20px; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            min-height: 100vh;
        }

        h1 { 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-top: 20px;
            margin-bottom: 30px; 
            color: #ffffff; 
            font-size: 26px;
            font-weight: bold;
        }

        /* Contenedor principal con el ID idéntico al del profesor */
        #list-estacion { 
            display: flex; 
            flex-direction: column; 
            justify-content: center;
            align-items: center; 
            gap: 20px; 
            width: 100%; 
            max-width: 420px; /* Ajustado al ancho real de la app */
        }

        /* Estilo exacto de las tarjetas del profesor */
        .estacion-btn { 
            background: rgba(33, 33, 52, 0.85); /* Fondo oscuro semitransparente */
            border: 1px solid rgba(255, 255, 255, 0.15); /* Borde fino sutil */
            border-radius: 10px; 
            padding: 18px 22px; 
            text-align: left; 
            color: white; 
            cursor: pointer; 
            transition: background 0.2s, transform 0.1s; 
            position: relative; 
            width: 100%;
            display: block;
        }

        .estacion-btn:hover { 
            background: rgba(40, 40, 65, 0.95);
            transform: scale(1.01);
        }

        .apodo { 
            font-size: 21px; 
            font-weight: bold; 
            margin-bottom: 4px; 
            display: block; 
            color: #ffffff;
        }

        .ubicacion { 
            font-size: 14px; 
            color: #cccccc; 
            display: block; 
            margin-bottom: 12px;
        }

        /* Contenedor inferior para el estado y las visitas */
        .info-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 24px;
            width: 100%;
        }

        /* El cartel rojo "Inactiva" idéntico */
        .estado { 
            display: inline-block; 
            background: #ff5757; 
            color: white; 
            font-size: 12px; 
            padding: 4px 10px; 
            border-radius: 6px; 
            font-weight: bold;
            text-transform: capitalize;
        }

        /* Visitas y el icono de la torre a la derecha */
        .visitas { 
            font-size: 14px; 
            color: #ffffff; 
            display: flex; 
            align-items: center; 
            gap: 6px; 
            margin-left: auto; /* Empuja a la derecha si no hay cartel */
            opacity: 0.85;
        }

        .icono-torre {
            font-size: 14px;
        }
    </style>
</head>
<body>

    <h1>Estaciones</h1>
    
    <div id="list-estacion"></div>

    <template id="tpl-btn-estacion">
        <button class="estacion-btn" type="button">
            <span class="apodo"></span>
            <span class="ubicacion"></span>
            <div class="info-footer">
                <span class="estado">Inactiva</span>
                <div class="visitas">
                    <span class="cant-visitas"></span>
                    <span class="icono-torre">🗼</span>
                </div>
            </div>
        </button>
    </template>

    <script>
        async function cargarEstaciones() {
            try {
                const response = await fetch('./proxy.php');
                const estaciones = await response.json();
                
                const container = document.getElementById('list-estacion');
                const template = document.getElementById('tpl-btn-estacion');

                container.innerHTML = "";

                estaciones.forEach(estacion => {
                    const clone = template.content.cloneNode(true);
                    
                    clone.querySelector('.apodo').textContent = estacion.apodo;
                    // El profesor usa el pin rojo directamente adelante del texto de ubicación
                    clone.querySelector('.ubicacion').textContent = `📍 ${estacion.ubicacion}`;
                    clone.querySelector('.cant-visitas').textContent = estacion.visitas !== undefined ? estacion.visitas : estacion.chipid;

                    // Lógica de Ingeniería Inversa: Ocultamos "Inactiva" en Tortuguitas
                    const cartelEstado = clone.querySelector('.estado');
                    if (estacion.apodo === "Tortuguitas") {
                        cartelEstado.style.display = 'none';
                    }

                    const boton = clone.querySelector('.estacion-btn');
                    boton.addEventListener('click', () => {
                        window.location.href = `detalle/${estacion.chipid}`;
                    });

                    container.appendChild(clone);
                });
                
            } catch (error) {
                console.error("Error al cargar las estaciones:", error);
            }
        }

        document.addEventListener('DOMContentLoaded', cargarEstaciones);
    </script>
</body>
</html>
