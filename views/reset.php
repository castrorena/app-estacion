<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <style>
        body { font-family: sans-serif; background: #1f1c2c; color: #fff; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .box { background: rgba(255,255,255,0.1); padding: 30px; border-radius: 12px; width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: none; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #ff758c; border: none; color: white; font-weight: bold; border-radius: 6px; cursor: pointer; }
        .msg { color: #ff6b6b; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Nueva Contraseña</h2>
        <?php if(!empty($error)): ?><p class="msg"><?= $error ?></p><?php endif; ?>
        <form method="POST" action="<?= BASE_URL ?>/reset/<?= htmlspecialchars($param) ?>">
            <input type="password" name="password" placeholder="Nueva contraseña" required>
            <input type="password" name="repassword" placeholder="Repetir contraseña" required>
            <button type="submit">Actualizar</button>
        </form>
    </div>
</body>
</html>
