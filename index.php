<?php
<<<<<<< HEAD
session_start();
require_once 'env.php';
require_once 'models/UsuarioModel.php';

$request = $_GET['slug'] ?? 'landing';
$parts = explode('/', trim($request, '/'));

$view = $parts[0] ?? 'landing';
$param = $parts[1] ?? null;

$user_ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

switch ($view) {
    case 'landing':
        require_once 'views/landing.php';
        break;

    case 'panel':
        require_once 'views/panel.php';
        break;

    case 'login':
        if (isset($_SESSION['user'])) { header("Location: " . BASE_URL . "/panel"); exit(); }
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = UsuarioModel::obtenerPorEmail($email);

            if (!$user) {
                $error = "Credenciales no válidas.";
            } elseif ($user['activo'] == 0) {
                $error = "Su usuario aún no se ha validado, revise su casilla de correo.";
            } elseif ($user['bloqueado'] == 1 || $user['recupero'] == 1) {
                $error = "Su usuario está bloqueado, revise su casilla de correo.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                $linkBlock = BASE_URL . "/blocked/" . $user['token'];
                $body = "<p>Has iniciado sesión en la app.</p><p>IP: $user_ip<br>Navegador: $user_agent</p><a href='$linkBlock'>No fui yo, bloquear cuenta</a>";
                UsuarioModel::enviarEmail($user['email'], "Aviso de Inicio de Sesión", $body);
                
                header("Location: " . BASE_URL . "/panel");
                exit();
            } else {
                $linkBlock = BASE_URL . "/blocked/" . $user['token'];
                $body = "<p>Intento de acceso fallido con contraseña inválida.</p><p>IP: $user_ip<br>Navegador: $user_agent</p><a href='$linkBlock'>No fui yo, bloquear cuenta</a>";
                UsuarioModel::enviarEmail($user['email'], "Intento de acceso fallido", $body);
                $error = "Credenciales no válidas.";
            }
        }
        require_once 'views/login.php';
        break;

    case 'register':
        if (isset($_SESSION['user'])) { header("Location: " . BASE_URL . "/panel"); exit(); }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombres = $_POST['nombres'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];

            if ($password !== $repassword) {
                $error = "Las contraseñas no coinciden.";
            } elseif (UsuarioModel::obtenerPorEmail($email)) {
                $error = "El email ya corresponde a un usuario registrado.";
            } else {
                $token_action = UsuarioModel::registrar($nombres, $email, $password);
                $linkValidate = BASE_URL . "/validate/" . $token_action;
                $body = "<h2>¡Bienvenido/a!</h2><p>Hacé clic abajo para activar tu cuenta:</p><a href='$linkValidate'>Click aquí para activar tu usuario</a>";
                UsuarioModel::enviarEmail($email, "Activar cuenta - App Estacion", $body);
                echo "Registro exitoso. Revisa tu correo electrónico para activar la cuenta.";
                exit();
            }
        }
        require_once 'views/register.php';
        break;

    case 'validate':
        if (isset($_SESSION['user'])) { header("Location: " . BASE_URL . "/panel"); exit(); }
        $user = UsuarioModel::obtenerPorTokenAction($param);
        if ($user && $user['activo'] == 0) {
            UsuarioModel::activarUsuario($user['id']);
            UsuarioModel::enviarEmail($user['email'], "Cuenta Activada", "Tu cuenta ya se encuentra activa.");
            header("Location: " . BASE_URL . "/login");
            exit();
        } else {
            echo "El token no corresponde a un usuario.";
        }
        break;

    case 'blocked':
        $user = UsuarioModel::obtenerPorToken($param);
        if ($user) {
            $token_action = UsuarioModel::bloquearUsuario($user['id']);
            $linkReset = BASE_URL . "/reset/" . $token_action;
            $body = "<p>Tu cuenta ha sido bloqueada.</p><a href='$linkReset'>Click aquí para cambiar contraseña</a>";
            UsuarioModel::enviarEmail($user['email'], "Cuenta Bloqueada", $body);
            echo "Usuario bloqueado, revise su correo electrónico.";
        } else {
            echo "El token no corresponde a un usuario.";
        }
        break;

    case 'recovery':
        if (isset($_SESSION['user'])) { header("Location: " . BASE_URL . "/panel"); exit(); }
        $msg = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $user = UsuarioModel::obtenerPorEmail($email);
            if ($user) {
                $token_action = UsuarioModel::iniciarRecupero($user['id']);
                $linkReset = BASE_URL . "/reset/" . $token_action;
                $body = "<p>Se inició el proceso de restablecimiento de contraseña.</p><a href='$linkReset'>Click aquí para restablecer contraseña</a>";
                UsuarioModel::enviarEmail($user['email'], "Restablecer Contraseña", $body);
                $msg = "Te enviamos un correo con los pasos a seguir.";
            } else {
                $msg = "El email no se encuentra registrado. <a href='".BASE_URL."/register'>Registrarse</a>";
            }
        }
        require_once 'views/recovery.php';
        break;

    case 'reset':
        if (isset($_SESSION['user'])) { header("Location: " . BASE_URL . "/panel"); exit(); }
        $user = UsuarioModel::obtenerPorTokenAction($param);
        if (!$user) {
            echo "El token no es válido.";
            exit();
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if ($password !== $repassword) {
                $error = "Las contraseñas no coinciden.";
            } else {
                UsuarioModel::resetPassword($user['id'], $password);
                $linkBlock = BASE_URL . "/blocked/" . $user['token'];
                $body = "<p>Se restableció tu contraseña con éxito.</p><p>IP: $user_ip<br>Navegador: $user_agent</p><a href='$linkBlock'>No fui yo, bloquear cuenta</a>";
                UsuarioModel::enviarEmail($user['email'], "Contraseña Restablecida", $body);
                header("Location: " . BASE_URL . "/login");
                exit();
            }
        }
        require_once 'views/reset.php';
        break;

    case 'detalle':
        if (!isset($_SESSION['user'])) {
            header("Location: " . BASE_URL . "/login");
            exit();
        }
        require_once 'views/detalle.php';
        break;

    default:
        http_response_code(404);
        echo "Página no encontrada";
=======
require_once 'env.php';

// Obtener la ruta de la URL (Simple router)
$request = isset($_GET['url']) ? $_file_url = $_GET['url'] : 'landing';
$urlParts = explode('/', rtrim($request, '/'));

$page = $urlParts[0];
$parameter = isset($urlParts[1]) ? $urlParts[1] : null; // Este será el chipid Z

// Función básica del motor de plantillas para renderizar layouts
function renderView($viewName, $data = []) {
    extract($data);
    // Cambiamos el diseño visual de forma creativa conservando la estructura solicitada
    include "views/pages/{$viewName}.php";
}

// Router básico
switch ($page) {
    case 'landing':
        renderView('landing');
        break;
    case 'panel':
        renderView('panel');
        break;
    case 'detalle':
        renderView('detalle', ['chipid' => $parameter]);
        break;
    default:
        header("Location: " . BASE_URL . "landing");
>>>>>>> 6f4eb6879d3c4a7c2ed1cad79196de2ff71331f9
        break;
}
?>
