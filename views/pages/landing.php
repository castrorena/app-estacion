<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estaciones Meteorológicas - Inicio</title>
    <style>
        body { background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%); font-family: 'Ubuntu', sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
        h1 { color: #4a5568; }
        button { background: #667eea; color: white; border: none; padding: 12px 24px; border-radius: 25px; font-size: 16px; cursor: pointer; transition: 0.3s; }
        button:hover { background: #764ba2; }
    </style>
</head>
<body>
    <div class="card">
        <h1>App Estación</h1>
        <p>Analizá y visualizá en tiempo real los datos provenientes de múltiples estaciones meteorológicas distribuidas en la región.</p>
        <button onclick="window.location.href='panel'">Ingresar al Panel</button>
    </div>
</body>
</html>
