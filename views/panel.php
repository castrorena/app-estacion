<?php
// Capturamos el chipid
$chipid = isset($_GET['chipid']) ? trim($_GET['chipid']) : null;
$estaciones = [];

// Función para consultar la API desde PHP
function obtenerDatosApi($url) {
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $json = curl_exec($ch);
        curl_close($ch);
        if ($json) return json_decode($json, true);
    }
    
    $arrContextOptions = [
        "ssl" => ["verify_peer" => false, "verify_peer_name" => false],
        "http" => ["header" => "User-Agent: Mozilla/5.0\r\n", "timeout" => 10]
    ];
    $json = @file_get_contents($url, false, stream_context_create($arrContextOptions));
    return $json ? json_decode($json, true) : null;
}

if (!$chipid) {
    $url = "http://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations";
    $resEstaciones = obtenerDatosApi($url);
    if (is_array($resEstaciones)) {
        $estaciones = $resEstaciones;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>app-estacion</title>

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;500;900&family=Ubuntu:wght@300;500;700&display=swap" rel="stylesheet"> 

	<!-- FontAwesome & Chart.js -->
	<script src="https://kit.fontawesome.com/2eb80ea257.js" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

	<style>
		*{
			padding: 0em;
			margin: 0em;
			box-sizing: border-box;
			font-family: 'Ubuntu', sans-serif;
		}

		body{
			background: rgb(236,81,43);
			background: linear-gradient(0deg, rgba(236,81,43,1) 0%, rgba(113,74,103,1) 24%, rgba(92,97,162,1) 100%);
			background-attachment: fixed;
			overscroll-behavior: contain;
			min-height: 100vh;
			color: white;
		}

		#wrapper{
			padding-top: 1em;
			display: flex;
			justify-content: center;
			align-items: center;
			flex-direction: column;
		}

		/* LISTA DE ESTACIONES */
		#list-estacion{
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			width: 100%;
		}

		#list-estacion-title{
			text-transform: uppercase;
			width: 300px;
			margin-bottom: 0.5em;
			font-size: 20px;
			font-weight: bold;
		}

		.btn-estacion{
			background-color: rgba(0,0,0,0.4);
			color: white;
			border-radius: 8px;
			text-decoration: none;
			margin-bottom: 0.5em;
			padding: 1em 1em;
			width: 300px;
			display: block;
			transition: background-color 0.2s ease;
		}

		.btn-estacion--inactiva::after{
			content: 'Inactiva';
			background: #f0aeae;
			color: red;
			border-radius: 8px;
			padding: .3rem;
			font-size: 12px;
			display: inline-block;
			margin-top: 5px;
		}

		.btn-estacion:hover{
			background-color: rgba(0,0,0,0.8);
		}

		.estacion-apodo{ font-size: 20px; font-weight: bold; }
		.estacion-ubicacion{ font-size: 14px; font-weight: lighter; }
		.estacion-visitas{ text-align: right; font-size: 14px; font-weight: lighter; }

		/* COLORES */
		.color-ubicacion{ color: red; }
		.color-visitas{ color: #f2834d; }
		.color-temperatura { color: #ffbf69; }
		.color-fuego { color: #ec512b; }
		.color-humedad { color: #00bbf9; }
		.color-viento { color: #e0fbfc; }
		.color-presion { color: #6ee55d; }
		.color-rojo { color: #ff5252; }
		.color-verde { color: #6ee55d; }

		/* DASHBOARD */
		#container {
			display: flex;
			gap: 15px;
			max-width: 1000px;
			width: 95%;
			margin: 0 auto;
		}

		#panel {
			flex: 2;
			background: rgba(18, 18, 28, 0.75);
			border-radius: 12px;
			padding: 20px;
			display: flex;
			flex-direction: column;
		}

		#menu a {
			color: white;
			font-size: 1.5rem;
			text-decoration: none;
		}

		#panel-title {
			margin-bottom: 15px;
		}

		#col-sub {
			display: flex;
			gap: 10px;
			font-size: 0.85rem;
			color: #ccc;
		}

		#title-ubicacion {
			font-size: 1.3rem;
			font-weight: bold;
			margin-top: 5px;
		}

		.panel-col {
			display: flex;
			justify-content: space-between;
			margin-bottom: 10px;
		}

		.col-important {
			display: flex;
			align-items: baseline;
		}

		.important-val-int {
			font-size: 3.5rem;
			font-weight: bold;
		}

		.important-detail {
			margin-left: 5px;
		}

		.important-val-unit {
			font-size: 1.2rem;
		}

		.important-val-dec {
			font-size: 1.2rem;
			color: #bbb;
		}

		.panel-row {
			display: flex;
			gap: 20px;
			margin-top: 10px;
		}

		.item-title {
			font-size: 0.85rem;
			color: #aaa;
		}

		.item-value {
			font-size: 1.1rem;
			font-weight: bold;
		}

		#panel-canvas {
			width: 100%;
			height: 250px;
			margin-top: auto;
		}

		#controls {
			flex: 1;
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 10px;
		}

		.btn-control {
			background: rgba(18, 18, 28, 0.75);
			border-radius: 12px;
			padding: 15px;
			display: flex;
			align-items: center;
			justify-content: center;
			text-align: center;
			cursor: pointer;
			transition: background-color 0.2s ease;
		}

		.btn-control:hover {
			background: rgba(30, 30, 45, 0.9);
		}

		#btn-viento {
			grid-column: span 2;
		}

		.control-title i {
			font-size: 2rem;
			margin-bottom: 5px;
		}

		.control-date {
			font-size: 1.2rem;
			font-weight: bold;
		}

		@media (max-width: 768px) {
			#container { flex-direction: column; }
			#controls { grid-template-columns: 1fr 1fr; }
		}
	</style>
</head>
<body>

	<div id="wrapper">

		<?php if ($chipid): ?>

			<div id="chipid" style="display: none;"><?= htmlspecialchars($chipid) ?></div>

			<div id="container">

				<div id="panel">
					
					<div id="menu">
						<a href="panel">
							<i class="fas fa-chevron-left"></i>
						</a>
					</div>

					<div id="panel-container">

						<div id="panel-title">
							<div id="col-sub">
								<div id="fecha"></div>
								<div id="hora"></div>
							</div>

							<div id="title-ubicacion">
								<i class="fas fa-map-marker-alt color-ubicacion"></i>
								<span id="ubicacion">Cargando...</span>
							</div>

							<div id="title-sub" style="display: none;">
								<i class="fas fa-thermometer-full color-temperatura"></i>&nbsp;TEMPERATURA
							</div>  
						</div>

						<!-- Sección Temperatura -->
						<div id="panel-container-temperatura">
							<div class="panel-col">
								<div class="col-items">
									<div class="item">
										<div class="item-title">
											<i class="fas fa-thermometer-full color-temperatura"></i>&nbsp;TEMPERATURA
										</div>
									</div>
								</div>

								<div class="col-items">
									<div class="col-important">
										<div class="important-val-int" id="temp-val-int">--</div>
										<div class="important-detail">
											<div class="important-val-unit">ºC</div>
											<div class="important-val-dec" id="temp-val-dec">--</div>
										</div>                          
									</div>

									<div class="panel-row">
										<div class="item">
											<div class="item-title"><i class="fas fa-caret-up color-rojo"></i>&nbsp;Máxima</div>
											<div class="item-value" id="temp-max">--ºC</div>
										</div>
										<div class="item">
											<div class="item-title"><i class="fas fa-caret-down color-verde"></i>&nbsp;Mínima</div>
											<div class="item-value" id="temp-min">--ºC</div>
										</div>
									</div>
								</div>
							</div>

							<div class="panel-col">
								<div class="col-items">
									<div class="item">
										<div class="item-title"><i class="fas fa-child color-humedad"></i>&nbsp;SENSACIÓN</div>
									</div>
								</div>

								<div class="col-items">
									<div class="col-important">
										<div class="important-val-int" id="sens-val-int">--</div>
										<div class="important-detail">
											<div class="important-val-unit">ºC</div>
											<div class="important-val-dec" id="sens-val-dec">--</div>
										</div>                          
									</div>

									<div class="panel-row">
										<div class="item">
											<div class="item-title"><i class="fas fa-caret-up color-rojo"></i>&nbsp;Máxima</div>
											<div class="item-value" id="sen-max">--ºC</div>
										</div>
										<div class="item">
											<div class="item-title"><i class="fas fa-caret-down color-verde"></i>&nbsp;Mínima</div>
											<div class="item-value" id="sen-min">--ºC</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Sección Fuego -->
						<div id="panel-container-fuego">
							<div class="panel-col">
								<div class="col-items">
									<div class="item"><div class="item-title">FFMC</div><div class="item-value" id="ffmc">--</div></div>
									<div class="item"><div class="item-title">DMC</div><div class="item-value" id="dmc">--</div></div>
									<div class="item"><div class="item-title">DC</div><div class="item-value" id="dc">--</div></div>
								</div>
							</div>
							<div class="panel-col">
								<div class="col-items">
									<div class="item"><div class="item-title">ISI</div><div class="item-value" id="isi">--</div></div>
									<div class="item"><div class="item-title">BUI</div><div class="item-value" id="bui">--</div></div>
									<div class="item"><div class="item-title">FWI</div><div class="item-value" id="fwi">--</div></div>
								</div>
							</div>
						</div>

						<!-- Sección Humedad -->
						<div id="panel-container-humedad">
							<div class="panel-col">
								<div class="col-items">
									<div class="col-important">
										<div class="important-val-int" id="humedad__val__int">--</div>
										<div class="important-detail">
											<div class="important-val-unit">%</div>
											<div class="important-val-dec" id="humedad__val__dec">--</div>
										</div>                          
									</div>
								</div>
							</div>                  
						</div>

						<!-- Sección Viento -->
						<div id="panel-container-viento">
							<div class="panel-col">
								<div class="col-items">
									<div class="item">
										<div class="item-title"><i class="fas fa-wind color-viento"></i>&nbsp;VIENTO</div>
									</div>
								</div>
								<div class="col-items">
									<div class="col-important">
										<div class="important-val-int" id="viento__val__int">--</div>
										<div class="important-detail">
											<div class="important-val-unit">Km/H</div>
											<div class="important-val-dec" id="viento__val__dec">--</div>
										</div>                          
									</div>
									<div class="panel-row">
										<div class="item">
											<div class="item-title"><i class="fas fa-caret-up color-rojo"></i>&nbsp;Máximo</div>
											<div class="item-value" id="viento__max">--Km/H</div>
										</div>
									</div>
								</div>
							</div>

							<div class="panel-col">
								<div class="panel-row">
									<div style="display: flex; align-items: center; gap:5px; font-size: 18px;">
										<i class="far fa-compass color-rojo"></i>   
										<div id="viento__val__veleta">--</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Sección Presión -->
						<div id="panel-container-presion">
							<div class="panel-col">
								<div class="col-items">
									<div class="col-important">
										<div class="important-val-int" id="presion__val__int">--</div>
										<div class="important-detail">
											<div class="important-val-unit">hPa</div>
											<div class="important-val-dec" id="presion__val__dec">--</div>
										</div>                          
									</div>
								</div>
							</div>              
						</div>

					</div>

					<!-- Gráfico -->
					<div id="panel-canvas">
						<div id="contenedor-grafico" class="chart-container" style="position: relative; height: 100%; width: 100%; padding: 0.5em;"> 
							<canvas id="myChart" style="width: 100%; height: 100%; background-color: rgba(0,0,0,0);"></canvas>
						</div>
					</div>

				</div>

				<div id="controls">
					<div class="btn-control" id="btn-temperatura">
						<div class="btn-control-container">
							<div class="control-title"><i class="fas fa-thermometer-full color-temperatura"></i></div> 
							<div id="temp" class="control-date">--ºC</div>
						</div>
					</div>

					<div class="btn-control" id="btn-fuego">
						<div class="btn-control-container">
							<div class="control-title"><i class="fas fa-fire color-fuego"></i></div> 
							<div id="fuego" class="control-date">?</div>
						</div>
					</div>

					<div class="btn-control" id="btn-humedad">
						<div class="btn-control-container">
							<div class="control-title"><i class="fas fa-tint color-humedad"></i></div> 
							<div id="humedad" class="control-date">--%</div>
						</div>
					</div>

					<div class="btn-control" id="btn-presion">
						<div class="btn-control-container">
							<div class="control-title"><i class="fas fa-arrow-circle-down color-presion"></i></div> 
							<div id="presion" class="control-date">--hPa</div>
						</div>
					</div>

					<div class="btn-control" id="btn-viento">
						<div class="btn-control-container">
							<div class="control-title"><i class="fas fa-wind color-viento"></i></div> 
							<div id="viento" class="control-date">--Km/H</div>
							<i class="visible far fa-compass color-rojo align-center"></i>  
							<div id="viento__direccion">--</div>
						</div>
					</div>
				</div>

			</div>

			<!-- JAVASCRIPT CORREGIDO (LLAMA A datos.php LOCAL) -->
			<script>
				let chipid = "";
				let fec = [];
				let tem = [];
				let hum = [];
				let vie = [];
				let fwi = [];
				let pre = [];

				const MAX_DATOS = 7;
				const INTERVAL_REFRESH = 60000;

				let dataJsonActual = "";

				let btnControls = [
					["temperatura", '<i class="fas fa-thermometer-full color-temperatura"></i>'],
					["fuego", '<i class="fas fa-fire color-fuego"></i>'],
					["humedad", '<i class="fas fa-tint color-humedad"></i>'],
					["viento", '<i class="fas fa-wind color-viento"></i>'],
					["presion", '<i class="fas fa-arrow-circle-down color-presion"></i>']
				];

				let sectionVisible = "";
				let myChart = null;

				document.addEventListener("DOMContentLoaded", function(event){
					chipid = document.querySelector("#chipid").innerHTML;
					console.log("Web Cargada para el chipid: " + chipid);

					addVisitStation();
					refreshDatos(MAX_DATOS);

					setInterval(refreshDatos, INTERVAL_REFRESH, 1);

					btnControls.forEach(function(btn, i){			
						if(btn[0] == "temperatura"){
							sectionVisible = btn[0];
							document.querySelector("#title-sub").innerHTML = btn[1] + '&nbsp;' + btn[0].toUpperCase();
							document.querySelector("#panel-container-" + btn[0]).setAttribute("style", "display: grid;");
						} else {
							document.querySelector("#panel-container-" + btn[0]).setAttribute("style", "display: none;");
						}
					});
					
					btnControls.forEach(function(element, index){
						document.querySelector("#btn-" + element[0]).addEventListener("click", event => {
							event.preventDefault();

							document.querySelector("#title-sub").innerHTML = element[1] + '&nbsp;' + element[0].toUpperCase();

							if(element[0] != "temperatura" && element[0] != "viento"){
								document.querySelector("#title-sub").setAttribute("style", "display: block;");
							} else {
								document.querySelector("#title-sub").setAttribute("style", "display: none;");
							}

							btnControls.forEach(function(btn, i){
								if(btn[0] == element[0]){
									sectionVisible = btn[0];
									document.querySelector("#panel-container-" + btn[0]).setAttribute("style", "display: grid;");
								} else {
									document.querySelector("#panel-container-" + btn[0]).setAttribute("style", "display: none;");
								}
							});

							procesar(dataJsonActual, false);
						});
					});
				});

				async function addVisitStation(){
					try {
						await fetch("datos.php?chipid=" + chipid + "&mode=visit-station");
					} catch(e) {}
				}

				async function refreshDatos(cantfilas){
					try {
						const response = await fetch("datos.php?chipid=" + chipid + "&cant=" + cantfilas);
						const data = await response.json();
						dataJsonActual = data;
						procesar(data);
					} catch(e) {
						console.error("Error al cargar datos:", e);
					}
				}

				function procesar(datos, addData = true){
					if (!datos || datos.length === 0) return;

					let hora = "";

					if(addData == true){
						fec = []; tem = []; hum = []; vie = []; fwi = []; pre = [];
						for (let i = datos.length - 1; i >= 0; i--) {
							hora = datos[i].fecha.split(" ")[1];

							fec.push(hora.split(":")[0] + ":" + hora.split(":")[1]);
							tem.push(datos[i].temperatura);
							hum.push(datos[i].humedad);
							vie.push(datos[i].viento);
							fwi.push(datos[i].fwi);
							pre.push(datos[i].presion);
						}

						if(fec[fec.length - 1] == fec[fec.length - 2]){
							fec.splice(fec.length - 1, 1);
							hum.splice(fec.length - 1, 1);
							tem.splice(fec.length - 1, 1);
							vie.splice(fec.length - 1, 1);
							fwi.splice(fec.length - 1, 1);
							pre.splice(fec.length - 1, 1);
						} else {
							fec.splice(0, 1);
							hum.splice(0, 1);
							tem.splice(0, 1);
							vie.splice(0, 1);
							fwi.splice(0, 1);
							pre.splice(0, 1);
						}
					}
					
					document.querySelector("#ubicacion").innerHTML = datos[0].ubicacion;
					document.querySelector("#fecha").innerHTML = datos[0].fecha.split(" ")[0] + "&nbsp;";
					document.querySelector("#hora").innerHTML = "&nbsp;" + datos[0].fecha.split(" ")[1];

					// Temperatura
					document.querySelector("#temp").innerHTML = datos[0].temperatura.split(".")[0] + "ºC";
					document.querySelector("#temp-val-int").innerHTML = datos[0].temperatura.split(".")[0];
					document.querySelector("#temp-val-dec").innerHTML = "." + (datos[0].temperatura.split(".")[1] || "0");
					document.querySelector("#temp-max").innerHTML = datos[0].tempmax + "ºC";
					document.querySelector("#temp-min").innerHTML = datos[0].tempmin + "ºC";

					let sens = datos[0].sensacion || datos[0].temperatura;
					document.querySelector("#sens-val-int").innerHTML = sens.split(".")[0];
					document.querySelector("#sens-val-dec").innerHTML = "." + (sens.split(".")[1] || "0");
					document.querySelector("#sen-max").innerHTML = (datos[0].sensamax || sens) + "ºC";
					document.querySelector("#sen-min").innerHTML = (datos[0].sensamin || sens) + "ºC";

					// Fuego
					document.querySelector("#fuego").innerHTML = fireDanger(datos[0].fwi);
					document.querySelector("#ffmc").innerHTML = datos[0].ffmc;
					document.querySelector("#dmc").innerHTML = datos[0].dmc;
					document.querySelector("#dc").innerHTML = datos[0].dc;
					document.querySelector("#isi").innerHTML = datos[0].isi;
					document.querySelector("#bui").innerHTML = datos[0].bui;
					document.querySelector("#fwi").innerHTML = datos[0].fwi;

					// Humedad
					document.getElementById("humedad").innerHTML = datos[0].humedad.split(".")[0] + "%";
					document.getElementById("humedad__val__int").innerHTML = datos[0].humedad.split(".")[0];
					document.getElementById("humedad__val__dec").innerHTML = "." + (datos[0].humedad.split(".")[1] || "0");

					// Viento
					document.getElementById("viento").innerHTML = datos[0].viento.split(".")[0] + "Km/H";
					document.getElementById("viento__val__int").innerHTML = datos[0].viento.split(".")[0];
					document.getElementById("viento__val__dec").innerHTML = "." + (datos[0].viento.split(".")[1] || "0");
					document.getElementById("viento__val__veleta").innerHTML = datos[0].veleta;
					document.getElementById("viento__direccion").innerHTML = datos[0].veleta;
					document.getElementById("viento__max").innerHTML = datos[0].maxviento.split(".")[0] + "Km/H";

					// Presión
					document.getElementById("presion").innerHTML = datos[0].presion.split(".")[0] + "hPa";
					document.getElementById("presion__val__int").innerHTML = datos[0].presion.split(".")[0];
					document.getElementById("presion__val__dec").innerHTML = "." + (datos[0].presion.split(".")[1] || "0");

					// Gráfico
					let itemsGrafico = "";
					if(sectionVisible == "temperatura"){
						itemsGrafico = [{ label: 'Temperatura', borderColor: '#ffbf69', data: tem }];
					} else if(sectionVisible == "humedad"){
						itemsGrafico = [{ label: 'Humedad', borderColor: '#00bbf9', data: hum }];
					} else if(sectionVisible == "viento"){
						itemsGrafico = [{ label: 'Viento', borderColor: '#e0fbfc', data: vie }];
					} else if(sectionVisible == "presion"){
						itemsGrafico = [{ label: 'Presion', borderColor: '#6ee55d', data: pre }];
					} else {
						itemsGrafico = [{ label: 'FWI', borderColor: '#ec512b', data: fwi }];
					}

					renderCharts(datos[0].ubicacion, fec, itemsGrafico);
				}

				function renderCharts(estacion, fecha, itemsGrafico){
					if(myChart != null){
						myChart.destroy();
					}

					const ctx = document.querySelector("#myChart").getContext("2d");

					myChart = new Chart(ctx, {
						type: "line",
						data: {
							labels: fecha,
							datasets: itemsGrafico
						},
						options: {
							scales: {
								yAxes: [{
									ticks: { beginAtZero: true, fontColor: 'white' }
								}],
								xAxes: [{
									ticks: { fontColor: 'white' }
								}]
							},
							legend: { display: false },
							tooltips: {
								backgroundColor: '#0584f6',
								titleFontSize: 20,
								xPadding: 20,
								yPadding: 20,
								mode: 'index'
							},
							elements: {
								line: { borderWidth: 2, fill: false },
								point: {
									radius: 6,
									borderWidth: 4,
									backgroundColor: 'white',
									hoverRadius: 8,
									hoverRadiusWidth: 4
								}
							},
							animation: { duration: 0 },
							responsiveAnimationDuration: 0,
							responsive: true,
							maintainAspectRatio: false
						}
					});
				}

				function fireDanger(fwi){
					let fwiFloat = parseFloat(fwi);
					if(fwiFloat >= 50) return "Extremo";
					if(fwiFloat >= 38) return "Muy alto";
					if(fwiFloat >= 21.3) return "Alto";
					if(fwiFloat >= 11.2) return "Moderado";
					if(fwiFloat >= 5.2) return "Bajo";
					return "Muy bajo";
				}
			</script>

		<?php else: ?>

			<!-- LISTA DE ESTACIONES -->
			<div id="list-estacion">
				<div id="list-estacion-title">estaciones</div>

				<?php if (!empty($estaciones) && is_array($estaciones)): ?>
					<?php foreach ($estaciones as $estacion): ?>
						<?php 
							$inactivaClass = (isset($estacion['dias_inactivo']) && $estacion['dias_inactivo'] > 0) ? 'btn-estacion--inactiva' : '';
						?>
						<a href="?chipid=<?= htmlspecialchars($estacion['chipid'] ?? '') ?>" class="btn-estacion <?= $inactivaClass ?>">
							<div class="estacion-apodo">
								<?= htmlspecialchars($estacion['apodo'] ?? '') ?>
							</div>

							<div class="estacion-ubicacion">
								<i class="fas fa-map-marker-alt color-ubicacion"></i>&nbsp;<?= htmlspecialchars(($estacion['provincia'] ?? '') . ', ' . ($estacion['ubicacion'] ?? '')) ?>
							</div>

							<div class="estacion-visitas">
								<?= htmlspecialchars($estacion['visitas'] ?? 0) ?>&nbsp;<i class="fa-solid fa-tower-observation color-visitas"></i>
							</div>
						</a>
					<?php endforeach; ?>
				<?php else: ?>
					<p style="color: white;">No se pudieron obtener las estaciones.</p>
				<?php endif; ?>
			</div>

		<?php endif; ?>

	</div>

</body>
</html>
