<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        body { font-family: sans-serif; background: #1f1c2c; color: #fff; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .box { background: rgba(255,255,255,0.1); padding: 30px; border-radius: 12px; width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: none; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #ff758c; border: none; color: white; font-weight: bold; border-radius: 6px; cursor: pointer; }
        a { color: #ff7eb3; text-decoration: none; font-size: 0.9rem; display: block; margin-top: 10px; }
        .msg { color: #ff6b6b; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Iniciar Sesión</h2>
        <?php if(!empty($error)): ?><p class="msg"><?= $error ?></p><?php endif; ?>
        <form method="POST" action="<?= BASE_URL ?>/login">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Acceder</button>
        </form>
        <a href="<?= BASE_URL ?>/recovery">¿Olvidaste tu contraseña?</a>
        <a href="<?= BASE_URL ?>/register">¿No tienes una cuenta? Registrarse</a>
    </div>
</body>
</html>
