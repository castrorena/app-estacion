<?php
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
        break;
}
?>
