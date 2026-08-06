<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Estación - Inicio</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1f1c2c, #928dab);
            color: #fff;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        h1 { font-size: 2.2rem; margin-bottom: 15px; }
        p { font-size: 1.1rem; line-height: 1.6; color: #e0e0e0; margin-bottom: 30px; }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #ff758c;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background-color: #ff7eb3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🌤️ App Estación</h1>
        <p>Monitoreá el estado del clima en tiempo real de distintas estaciones meteorológicas locales. Consultá temperatura, humedad, viento y más.</p>
        <a href="<?= BASE_URL ?>/panel" class="btn">Ver Estaciones</a>
    </div>
</body>
</html>
