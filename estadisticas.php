<?php
// Archivo de conexión a la base de datos
session_start();
include_once("./conexion.php");
// Asegúrate de tener este archivo con la conexión

mysqli_begin_transaction($conn);

// Consulta para obtener las mesas ocupadas de la terraza
$queryTerraza = "SELECT t1.id_mesa, COUNT(t2.id_ocupacion) AS ocupaciones 
                FROM tbl_mesa t1
                LEFT JOIN tbl_ocupacion t2 ON t1.id_mesa = t2.id_mesa
                LEFT JOIN tbl_sala t3 ON t1.id_sala = t3.id_sala
                WHERE t3.tipo_sala = 'terraza'
                GROUP BY t1.id_mesa
                ORDER BY ocupaciones DESC";

$resultTerraza = mysqli_query($conn, $queryTerraza);
$rowsTerraza = mysqli_fetch_all($resultTerraza, MYSQLI_ASSOC);

// Consulta para obtener las mesas ocupadas del comedor
$queryComedor = "SELECT t1.id_mesa, COUNT(t2.id_ocupacion) AS ocupaciones 
                FROM tbl_mesa t1
                LEFT JOIN tbl_ocupacion t2 ON t1.id_mesa = t2.id_mesa
                LEFT JOIN tbl_sala t3 ON t1.id_sala = t3.id_sala
                WHERE t3.tipo_sala = 'comedor'
                GROUP BY t1.id_mesa
                ORDER BY ocupaciones DESC";

$resultComedor = mysqli_query($conn, $queryComedor);
$rowsComedor = mysqli_fetch_all($resultComedor, MYSQLI_ASSOC);

// Consulta para obtener las mesas ocupadas de la sala privada
$queryPrivada = "SELECT t1.id_mesa, COUNT(t2.id_ocupacion) AS ocupaciones 
                FROM tbl_mesa t1
                LEFT JOIN tbl_ocupacion t2 ON t1.id_mesa = t2.id_mesa
                LEFT JOIN tbl_sala t3 ON t1.id_sala = t3.id_sala
                WHERE t3.tipo_sala = 'privada'
                GROUP BY t1.id_mesa
                ORDER BY ocupaciones DESC";
$resultPrivada = mysqli_query($conn, $queryPrivada);
$rowsPrivada = mysqli_fetch_all($resultPrivada, MYSQLI_ASSOC);  

$queryHoras = "SELECT HOUR(fecha_inicio) AS hora,
                COUNT(*) AS ocupaciones
                FROM
                tbl_ocupacion
                GROUP BY
                hora
                ORDER BY
                ocupaciones DESC;";
$resultHoras= mysqli_query($conn, $queryHoras);
$rowsHoras = mysqli_fetch_all($resultHoras, MYSQLI_ASSOC);


// Cerrar la conexión
// mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Mesas</title>
    <link rel="stylesheet" href="./estadisticas.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body><!-- Título de la página -->
<h1>Estadísticas de Mesas</h1>

<!-- Encabezado y gráfico para la terraza más ocupada -->
<h2>Terraza más ocupada</h2>
<div>
    <!-- Canvas donde se mostrará el gráfico de barras para la terraza -->
    <canvas id="terrazaGrafico"></canvas>
</div>

<h2>Comedor más ocupado</h2>
<div>
    <!-- Canvas donde se mostrará el gráfico de barras para el comedor -->
    <canvas id="comedorGrafico"></canvas>
</div>

<!-- Encabezado y gráfico para la sala privada más ocupada -->
<h2>Sala Privada más ocupada</h2>
<div>
    <!-- Canvas donde se mostrará el gráfico de barras para la sala privada -->
    <canvas id="privadaGrafico"></canvas>
</div>

<!-- Encabezado y gráfico para la hora  más ocupada -->
<h2>Hora más ocupada</h2>

<div>
    <!-- Canvas donde se mostrará el gráfico de barras para la hora en la terraza -->
    <canvas id="horaGrafico"></canvas>
</div>

<script>

    // GRAFICO TERRAZA
    // Declaramos una varible terrazaData y le asignamos el array $rowsTerraza (contiene la info mesasTerrazasOcupadas) en formato JSON
    var terrazaData = <?php echo json_encode($rowsTerraza); ?>;
    // creamos un array que contiene los ids de las mesas en la terraza.
    var terrazaID = terrazaData.map(row => row.id_mesa);
    // creamoa otro array que contiene las ocupaciones de la terraza
    var terrazaOcupaciones = terrazaData.map(row => row.ocupaciones);
    // buscamos el canvas que hemos creado antes y lo guardamos en una variable pero antes le definimos como queremos el gráfico (en este caso 2d)
    var terrazaCanvas = document.getElementById('terrazaGrafico').getContext('2d');
    // creamos una variable para guardar el grafico
    var terrazaGrafico = new Chart(terrazaCanvas, {
        // definimos el tipo de grafico que queremos
        type: 'bar',
        data: {
            // Etiquetas en el eje X
            labels: terrazaID,
            datasets: [{
                // nombre del cuadrado que explica de muestra el gráfico
                label: 'Ocupaciones',
                data: terrazaOcupaciones,
                backgroundColor: '#86c04b66',
                // backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: '#86C04B',
                // borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                barThickness: 55
            }]
        }
    });

    // GRAFICO COMEDOR
    var comedorData = <?php echo json_encode($rowsComedor); ?>;
    var comedorID = comedorData.map(row => row.id_mesa);
    var comedorOcupaciones = comedorData.map(row => row.ocupaciones);

    var comedorCanvas = document.getElementById('comedorGrafico').getContext('2d');
    var comedorGrafico = new Chart(comedorCanvas, {
        type: 'bar',
        data: {
            labels: comedorID,
            datasets: [{
                label: 'Ocupaciones',
                data: comedorOcupaciones,
                backgroundColor: '#4b86c066',
                borderColor: '#4b86c0',
                borderWidth: 1,
                barThickness: 55 
            }]
        }
    });

    // GRAFICO SALA PRIVADA
    var privadaData = <?php echo json_encode($rowsPrivada); ?>;
    var privadaID = privadaData.map(row => row.id_mesa);
    var privadaOcupaciones = privadaData.map(row => row.ocupaciones);

    var privadaCanvas = document.getElementById('privadaGrafico').getContext('2d');
    var privadaGrafico = new Chart(privadaCanvas, {
        type: 'bar',
        data: {
            labels: privadaID,
            datasets: [{
                label: 'Ocupaciones',
                data: privadaOcupaciones,
                backgroundColor: '#864bc066',
                borderColor: '#864BC0',
                borderWidth: 1,
                // anchura barras
                barThickness: 55
            }]
        }
    });

    // GRÁFICO HORA MÁS OCUPADA
    var horaData = <?php echo json_encode($rowsHoras); ?>;
    var horaID = horaData.map(row => row.hora);
    var horaOcupaciones = horaData.map(row => row.ocupaciones);

    // buscamos el canvas que hemos creado antes y lo guardamos en una variable pero antes le definimos como queremos el gráfico (en este caso 2d)
    var horaCanvas = document.getElementById('horaGrafico').getContext('2d');
    // 
    var horaGrafico = new Chart(horaCanvas, {
        type: 'bar',
        data: {
            labels: horaID,
            datasets: [{
                label: 'Ocupaciones',
                data: horaOcupaciones,
                backgroundColor: '#c04b8559',
                borderColor: '#c04b85',
                borderWidth: 1,
                barThickness: 55
            }]
        }
    });
</script>
</body>
</html>
