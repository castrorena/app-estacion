<?php
// Permitimos que tu propio JavaScript pueda leer esto
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

$apiUrl = 'https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations';

// Usamos file_get_contents para leer la API del profesor (esto no tiene bloqueos de CORS)
$jsonData = file_get_contents($apiUrl);

// Devolvemos los datos tal cual
echo $jsonData;
?>
