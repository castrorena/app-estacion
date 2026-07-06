<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Estación</title>
    <style>
        body { background: #1e1e2f; color: #fff; font-family: 'Ubuntu', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .detalle-box { background: #27293d; border: 2px solid #e14eca; padding: 40px; border-radius: 15px; text-align: center; }
        a { color: #00f2c4; text-decoration: none; margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>
    <div class="detalle-box">
        <h2>Detalle de la Estación</h2>
        <p><strong>ChipID Seleccionado (Z):</strong> <?php echo htmlspecialchars($chipid); ?></p>
        <p>Ubicación y métricas avanzadas (Próximamente en la siguiente etapa).</p>
        <a href="../panel">⬅ Volver al Panel</a>
    </div>
</body>
</html>
