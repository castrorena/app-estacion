<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Estación - Panel</title>
    <!-- Librería Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1f1c2c, #928dab);
            color: #fff;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto 20px auto;
        }
        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        @media (max-width: 900px) {
            .grid-container { grid-template-columns: 1fr; }
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        .card h3 { margin-top: 0; color: #ff758c; font-size: 1.1rem; }
        .card .value { font-size: 2rem; font-weight: bold; margin: 10px 0; }
        .chart-card { grid-column: span 1; }
    </style>
</head>
<body>

    <div class="header">
        <a href="<?= BASE_URL ?>/panel" class="btn-back">← Volver</a>
        <div>
            <h1 id="estacion-apodo" style="margin:0;">Cargando...</h1>
            <small id="estacion-ubicacion">📍 -</small>
        </div>
        <div></div>
    </div>

    <div class="grid-container">
        <!-- Gráfico principal de Temperatura -->
        <div class="card chart-card" style="grid-row: span 2;">
            <h3>📈 Histórico de Temperatura</h3>
            <canvas id="tempChart"></canvas>
        </div>

        <!-- Indicadores -->
        <div class="card">
            <h3>🌡️ Temperatura</h3>
            <div class="value" id="val-temp">-- °C</div>
        </div>

        <div class="card">
            <h3>🔥 Riesgo de Incendio</h3>
            <div class="value" id="val-fwi">--</div>
        </div>

        <div class="card">
            <h3>💧 Humedad</h3>
            <div class="value" id="val-hum">-- %</div>
        </div>

        <div class="card">
            <h3>📉 Presión</h3>
            <div class="value" id="val-pres">-- hPa</div>
        </div>

        <div class="card" style="grid-column: span 2;">
            <h3>💨 Viento</h3>
            <div class="value" id="val-viento">-- Km/H</div>
        </div>
    </div>

    <script>
        const chipid = '<?= $param ?>';
        let tempChart = null;

        // Inicializar gráfico de Chart.js
        function initChart() {
            const ctx = document.getElementById('tempChart').getContext('2d');
            tempChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Temperatura (°C)',
                        data: [],
                        borderColor: '#ff758c',
                        backgroundColor: 'rgba(255, 117, 140, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { ticks: { color: '#fff' } },
                        y: { ticks: { color: '#fff' } }
                    },
                    plugins: { legend: { labels: { color: '#fff' } } }
                }
            });
        }

        async function cargarDatos() {
            try {
                // Consultar información en tiempo real
                const res = await fetch(`https://mattprofe.com.ar/proyectos/app-estacion/api/estacion/${chipid}`);
                const data = await res.json();

                if (data) {
                    // Actualizar encabezados
                    document.getElementById('estacion-apodo').textContent = data.apodo || 'Estación';
                    document.getElementById('estacion-ubicacion').textContent = `📍 ${data.ubicacion || ''}`;

                    // Actualizar métricas
                    document.getElementById('val-temp').textContent = `${data.temperatura ?? '--'} °C`;
                    document.getElementById('val-hum').textContent = `${data.humedad ?? '--'} %`;
                    document.getElementById('val-viento').textContent = `${data.viento ?? '--'} Km/H`;
                    document.getElementById('val-pres').textContent = `${data.presion ?? '--'} hPa`;
                    document.getElementById('val-fwi').textContent = data.fwi || 'Normal';

                    // Actualizar gráfico con el historial
                    if (data.historial && tempChart) {
                        const labels = data.historial.map(item => item.hora);
                        const temps = data.historial.map(item => item.temperatura);

                        tempChart.data.labels = labels;
                        tempChart.data.datasets[0].data = temps;
                        tempChart.update();
                    }
                }
            } catch (error) {
                console.error("Error al cargar datos:", error);
            }
        }

        // Ejecución al iniciar
        initChart();
        cargarDatos();

        // Actualización automática cada 60 segundos (1 minuto)
        setInterval(cargarDatos, 60000);
    </script>
</body>
</html>
