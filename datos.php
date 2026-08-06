<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Capturamos los parámetros que envía el JavaScript
$chipid = $_GET['chipid'] ?? '';
$cant   = $_GET['cant'] ?? '';
$mode   = $_GET['mode'] ?? '';

// Construimos la URL hacia la API oficial
$apiUrl = "http://mattprofe.com.ar/proyectos/app-estacion/datos.php?";

if ($mode === 'visit-station') {
    $apiUrl .= "chipid=" . urlencode($chipid) . "&mode=visit-station";
} elseif ($mode === 'list-stations') {
    $apiUrl .= "mode=list-stations";
} else {
    $apiUrl .= "chipid=" . urlencode($chipid) . "&cant=" . urlencode($cant);
}

// Consultamos la API desde el servidor
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
