<?php
//Iniciamos sesión
session_start();
//Hacemos conexión
include_once("./conexion.php");

// Comprobar si el usuario ha iniciado sesión
// if (!isset($_SESSION['usuario'])) {
//     header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
//     exit();
// }
?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        $(".terraza").on("click", function() {
            $(".terraza_opciones").toggleClass("visible");
        });
    });
</script>

<header>
    <a href="./index.php"><img id="logo" src="./src/LOGO3.png" alt="logo"></a>
    <form method="post" action="mostrar_mesas.php">
        <input type='submit' name='terraza_1' value="Terraza 1" class="terraza secciones">
    </form>
    <form method="post" action="mostrar_mesas.php">
        <input type='submit' name='comedor_1' value="Comedor 1" class="comedor secciones">
    </form>
    <form method="post" action="mostrar_mesas.php">
        <input type='submit' name='sala_privada_1' value="Sala Privada 1" class=" sala secciones">
    </form>
    <form action="">
        <input type='submit' name='cerrar_sesion' value="Cerrar Sesion" class=" sala secciones">
    </form>
    <hr class="hr-header">
    <!-- TERRAZAS -->
    <div class="terraza_opciones salas flex">
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_1' value="terraza_1" class="secciones-secund">
        </form>

        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_2' value="terraza_2" class="secciones-secund">
        </form>

        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_3' value="terraza_3" class="secciones-secund">
        </form>

        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_4' value="terraza_4" class="secciones-secund">
        </form>
    </div>
    <!-- COMEDOR -->
    <div class='comedor_opciones salas flex'>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='comedor_1' value="comedor_1" class="secciones-secund">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='comedor_2' value="comedor_2" class="secciones-secund">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='comedor_3' value="comedor_3" class="secciones-secund">
        </form>
    </div>
    <!-- SALA -->
    <div class='sala_opciones salas flex'>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='sala_1' value="sala_privada_1" class="secciones-secund">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='sala_2' value="sala_privada_2" class="secciones-secund">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='sala_3' value="sala_privada_3" class="secciones-secund">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='sala_4' value="sala_privada_4" class="secciones-secund">
        </form>
    </div>
</header>


<?php
// Función para mostrar las mesas ocupadas por los camareros que más mesas han ocupado
function mostrarCamarerosOrdenadosPorMesas($conn)
{
    try {
        // Consulta SQL para mostrar los camareros ordenados por la cantidad de mesas que han ocupado con la fecha de inicio correspondiente con la ocupación
        $sqlCamareros = "
    SELECT
        c.nombre as nombre_camarero,
        COUNT(o.id_mesa) as num_mesas_ocupadas,
        GROUP_CONCAT(o.id_mesa ORDER BY o.id_mesa) as mesas_ocupadas_ids,
        GROUP_CONCAT(o.num_veces_ocupada ORDER BY o.id_mesa) as veces_ocupada,
        GROUP_CONCAT(o.id_ocupacion ORDER BY o.fecha_inicio) as ocupacion_ids,
        GROUP_CONCAT(DISTINCT o.fecha_inicio ORDER BY o.fecha_inicio) as fechas_inicio
    FROM tbl_camarero c
    LEFT JOIN (
        SELECT
            o.id_camarero,
            o.id_mesa,
            COUNT(*) as num_veces_ocupada,
            GROUP_CONCAT(o.id_ocupacion) as id_ocupacion,
            GROUP_CONCAT(o.fecha_inicio ORDER BY o.fecha_inicio) as fecha_inicio
        FROM tbl_ocupacion o
        GROUP BY o.id_camarero, o.id_mesa
    ) o ON c.id_camarero = o.id_camarero
    GROUP BY c.id_camarero
    ORDER BY num_mesas_ocupadas DESC;
";

        // Ejecutar la consulta
        $stmtCamareros = mysqli_prepare($conn, $sqlCamareros);
        mysqli_stmt_execute($stmtCamareros);
        $resultCamareros = mysqli_stmt_get_result($stmtCamareros);

        if (!$resultCamareros) {
            die("Error en la consulta: " . mysqli_error($conn));
        }

        //Si la consulta da resultados muestra toda la información que recoge la consulta
        if (mysqli_num_rows($resultCamareros) > 0) {

            //Recorre los resultados
            while ($row = mysqli_fetch_assoc($resultCamareros)) {
                echo "<p>------------------------</p>";
                //Muestra todos los camareros y el numero de mesas que han ocupado
                echo "<p>Camarero: " . $row['nombre_camarero'] . " - Mesas Ocupadas: " . $row['num_mesas_ocupadas'] . "</p>";
                echo "<br>";
                //Si ha ocupado alguna mesa entra en el if si no mostrará solo la linea de arriba
                if ($row['num_mesas_ocupadas'] > 0) {
                    
                    //Se mostrarán todas las mesas que ha ocupado un camarero
                    echo "<p>Mesas Ocupadas:</p>";
                    echo "<br>";

                    //Utilizamos la función explode para dividir una cadena en un array de subcadenas, es decir, para hacer un subarray
                    $mesasIds = explode(",", $row['mesas_ocupadas_ids']);
                    $vecesOcupada = explode(",", $row['veces_ocupada']);
                    $fechasInicio = explode(",", $row['fechas_inicio']);

                    //Inicializamos un contador para que se le sume cuando entre en el for
                    $totalVecesOcupadas = 0;

                    //Se hará un for para contar las mesas y las veces que se han ocupado y las ocupaciones totales
                    for ($i = 0; $i < count($mesasIds); $i++) {
                        if ($mesasIds[$i] != null && $vecesOcupada[$i] != null) {
                            echo "Mesa " . $mesasIds[$i] . " - Veces Ocupada: " . $vecesOcupada[$i];
                            $totalVecesOcupadas += $vecesOcupada[$i];

                            // Si la mesa se ha ocupado más de una vez, mostrar la ocupación con su fechas correspondientes
                            if ($vecesOcupada[$i] > 1) {
                                echo "<ul>";
                                for ($j = 0; $j < $vecesOcupada[$i]; $j++) {
                                    echo "<li>Ocupación " . ($j + 1) . ": " . $fechasInicio[$i * $vecesOcupada[$i] + $j] . "</li>";
                                }
                                echo "</ul>";
                            } else {
                                if ($fechasInicio[$i] != null) {
                                    echo " - Fecha Inicio: " . $fechasInicio[$i];
                                    echo "<br>";
                                }
                            }

                            echo "<br>";
                            echo "<br>";
                        }
                    }
                    //Mostramos las ocupaciones totales
                    echo "Total ocupaciones: " . $totalVecesOcupadas;
                }
            }
        } else {
            echo "<p>No hay resultados.</p>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['capacidadFiltro'])) {
        $_SESSION['capacidadFiltro'] = $_POST['capacidadFiltro'];
    }
    if (isset($_POST['fechaFiltro'])) {
        $_SESSION['fechaFiltro'] = $_POST['fechaFiltro'];
    }
}

function filtrarMesasPorCapacidad($conn, $capacidadFiltro)
{
    try {
        // Consulta SQL para filtrar las mesas disponibles por capacidad
        $sqlFiltro = "SELECT m.id_mesa, m.capacidad, s.nombre as sala_nombre 
        FROM tbl_mesa m 
        INNER JOIN tbl_sala s ON m.id_sala = s.id_sala 
        WHERE m.capacidad = ? AND m.ocupada = 0 
        ORDER BY m.capacidad";
        // Ejecutar la consulta
        $stmtFiltro = mysqli_prepare($conn, $sqlFiltro);
        mysqli_stmt_bind_param($stmtFiltro, "i", $capacidadFiltro);
        mysqli_stmt_execute($stmtFiltro);
        $resultFiltro = mysqli_stmt_get_result($stmtFiltro);

        //Mostraremos las mesa que recoja en la consulta en función del valor de la variable $capacidadFiltro que será la capacidad de la mesa que queremos filtrar
        echo "<h2>Filtradas por capacidad: $capacidadFiltro personas</h2>";
        echo "<br>";
        if ($resultFiltro) {
            if (mysqli_num_rows($resultFiltro) > 0) {

                while ($row = mysqli_fetch_assoc($resultFiltro)) {
                    echo "<p>Mesa: " . $row['id_mesa'] . " - Capacidad: " . $row['capacidad'] . " - Sala: " . $row['sala_nombre'] . "</p>";
                }
            } else {
                echo "<br>";
                echo "<p>No hay mesas disponibles con la capacidad seleccionada.</p>";
            }
        } else {
            throw new Exception("Error en la consulta de filtrado: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}


// Verificamos que el formulario ha sido enviado por post y si contiene un campo llamado capacidadFiltro

function filtrarMesasPorFecha($conn, $fechaFiltro)
{
    try {
        // Consulta SQL para filtrar mesas por fecha en la que se ocupó
        $sqlFiltroFecha = "SELECT m.id_mesa, s.nombre AS nombre_sala, o.fecha_inicio, o.fecha_fin
            FROM tbl_ocupacion o
            JOIN tbl_mesa m ON o.id_mesa = m.id_mesa
            JOIN tbl_sala s ON m.id_sala = s.id_sala
            WHERE  o.fecha_fin IS NOT NULL AND DATE (o.fecha_inicio) = ?
            ORDER BY o.fecha_inicio";
        // Ejecutar la consulta
        $stmtFiltroFecha = mysqli_prepare($conn, $sqlFiltroFecha);
        mysqli_stmt_bind_param($stmtFiltroFecha, "s", $fechaFiltro);
        mysqli_stmt_execute($stmtFiltroFecha);
        $resultFiltroFecha = mysqli_stmt_get_result($stmtFiltroFecha);

        echo "<br>";
        //Se comprobará que haya resultados y si hay se mostrarán todas las mesas que recoja la consulta
        echo "<h4>$fechaFiltro</h4>";
        if ($resultFiltroFecha) {
            if (mysqli_num_rows($resultFiltroFecha) > 0) {

                while ($row = mysqli_fetch_assoc($resultFiltroFecha)) {
                    echo "<br>";
                    echo "ID Mesa: " . $row["id_mesa"] . "<br>";
                    echo "Sala: " . $row["nombre_sala"] . "<br>";
                    echo "Fecha Inicio: " . $row["fecha_inicio"] . "<br>";
                    echo "Fecha Fin: " . $row["fecha_fin"] . "<br>";
                }
            } else {
                // Si no hay resultado mostrará este texto
                echo "<p>No hay ocupaciones de mesas en la fecha seleccionada.</p>";
            }
        } else {
            throw new Exception("Error en la consulta de filtrado por fecha: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/home.css">
    <!-- Añadimos una función que recoge los botones para mostrar u ocultar los resultados de los filtros en función del valor de la variable filterId. 
        Le asignaremos las clases hidden para esconder y visible para mostrar -->
    <script>
        function toggleFilter(filterId) {
            var filter = document.getElementById(filterId);
            filter.classList.toggle("hidden");
            filter.classList.toggle("visible");
        }
    </script>
</head>

<body>
    <div class="row flex" id="">
        <div id="restaurante">

            <?php
            function mostrarMesas($nombreSala, $conn)
            {

                // -- Consulta para mostrar las mesas de una sala específica
                $sqlSala = "SELECT ms.id_mesa, ms.capacidad, ms.ocupada 
            FROM tbl_mesa ms
            JOIN tbl_sala sl ON ms.id_sala = sl.id_sala
            WHERE sl.nombre = ?";

                // Ejecutar la consulta
                $stmtMesas = mysqli_prepare($conn, $sqlSala);
                mysqli_stmt_bind_param($stmtMesas, "s", $nombreSala);
                mysqli_stmt_execute($stmtMesas);
                $resultSala = mysqli_stmt_get_result($stmtMesas);

                if ($resultSala) {
                    // Variable para almacenar la clase del formulario
                    $claseFormulario = '';

                    // Determinar la clase del formulario según el número de mesas
                    $numMesas = mysqli_num_rows($resultSala);
                    if ($numMesas == 2) {
                        $claseFormulario = 'dos-mesas';
                    } elseif ($numMesas == 4) {
                        $claseFormulario = 'cuatro-mesas';
                    } elseif ($numMesas == 6) {
                        $claseFormulario = 'seis-mesas';
                    }
                    echo "<h2 class='migadepan'>Mesas de $nombreSala</h2>";
                    echo "<form method='post' action='cambiar_estado_mesa.php' class='sala-distribucion $claseFormulario'>";
                    // echo "<form method='post' action='cambiar_estado_mesa.php' class='sala-distribucion'>";
                    while ($row = mysqli_fetch_assoc($resultSala)) {
                        echo "<button type='submit' name='mesa_id' value='" . $row['id_mesa'] . "' ";

                        // Concatenar clases para capacidad
                        echo "class='mesa-" . $row['capacidad'];

                        // Concatenar clases para capacidad


                        // Concatenar clases adicionales para ocupación
                        if ($row['ocupada']) {
                            echo "-ocupada";
                        }

                        echo " mesa-fondo";
                        echo "'>";
                        // echo "'>Mesa " . $row['id_mesa'] . " - Capacidad: " . $row['capacidad'];

                        echo "</button>";
                    }
                    echo "</form>";
                } else {
                    echo "Error en la consulta: " . mysqli_error($conn);
                }
            }


            //Dependiendo del input que se presione hará una llamada a la función de mostrar mesas con el valor que se le pasa correspondiente
            //Si no entra ni en el if ni en los elseif saldrá del bucle
            if (isset($_POST['terraza_1'])) {
                mostrarMesas('terraza_1', $conn);
            } elseif (isset($_POST['terraza_2'])) {
                mostrarMesas('terraza_2', $conn);
            } elseif (isset($_POST['terraza_3'])) {
                mostrarMesas('terraza_3', $conn);
            } elseif (isset($_POST['terraza_4'])) {
                mostrarMesas('terraza_4', $conn);
            } elseif (isset($_POST['comedor_1'])) {
                mostrarMesas('comedor_1', $conn);
            } elseif (isset($_POST['comedor_2'])) {
                mostrarMesas('comedor_2', $conn);
            } elseif (isset($_POST['comedor_3'])) {
                mostrarMesas('comedor_3', $conn);
            } elseif (isset($_POST['sala_privada_1'])) {
                mostrarMesas('sala_privada_1', $conn);
            } elseif (isset($_POST['sala_privada_2'])) {
                mostrarMesas('sala_privada_2', $conn);
            } elseif (isset($_POST['sala_privada_3'])) {
                mostrarMesas('sala_privada_3', $conn);
            } elseif (isset($_POST['sala_privada_4'])) {
                mostrarMesas('sala_privada_4', $conn);
            } else {
                exit();
            }

            // Establecer valores predeterminados para los filtros si no están configurados
            if (!isset($_SESSION['capacidadFiltro'])) {
                $_SESSION['capacidadFiltro'] = null;
            }

            if (!isset($_SESSION['fechaFiltro'])) {
                $_SESSION['fechaFiltro'] = null;
            }
            ?>

        </div>
        <div id="filtro">
            <div class="filtros-separaciones">
                <div class="margen-1">
                    <h2 class="filtro-margin-top">Mesas Disponibles</h2>
                    <!-- Formulario del filtro de capacidad -->
                    <form action="mostrar_mesas.php" method="post">
                        <select name="capacidadFiltro" class="select-personas">
                            <option disabled selected>Selecciona opción</option>
                            <option value="2">2 personas</option>
                            <option value="3">3 personas</option>
                            <option value="4">4 personas</option>
                            <option value="6">6 personas</option>
                            <option value="8">8 personas</option>
                            <option value="10">10 personas</option>
                            <option value="15">15 personas</option>
                        </select>
                        <input class="aceptar-select-personas" type="submit" value="Enviar">
                    </form>
                    <!-- Si detecta la variable de sesión del filtro de capacidad se llamará a la función correspondiente con el valor del select -->
                    <div class="margen-2-primera">
                        <div class="visible" id="capacidadFilter">
                            <?php

                            if (isset($_SESSION['capacidadFiltro'])) {
                                echo "<div id='capacidadFilter' class='visible'>";
                                filtrarMesasPorCapacidad($conn, $_SESSION['capacidadFiltro']);
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Este botón muestra u oculta los resultados del filtro de capacidad cuando pulsas en él -->
                <button class="botones-ocultar" onclick="toggleFilter('capacidadFilter')">Mostrar/Ocultar Filtro de Capacidad</button>
            </div>

            <div class="filtros-separaciones">
                <div class="margen-1">
                    <h2 class="filtro-margin-top">Camareros</h2>
                    <h4>(Ordenados por la cantidad de mesas ocupadas)</h4>
                    <br>
                    <div class="margen-2-segunda">
                        <?php

                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            // Solo asignar las variables de sesión si el formulario ha sido enviado
                            if (isset($_POST['capacidadFiltro'])) {
                                $_SESSION['capacidadFiltro'] = $_POST['capacidadFiltro'];
                            }
                            if (isset($_POST['fechaFiltro'])) {
                                $_SESSION['fechaFiltro'] = $_POST['fechaFiltro'];
                            }
                        }
                        //Llamamos a la función del filtro de camareros para que se muestre
                        echo "<div id='camareroFilter' class='visible'>";
                        mostrarCamarerosOrdenadosPorMesas($conn);
                        echo "</div>";
                        ?>
                    </div>
                </div>
                <!-- Este botón muestra u oculta los resultados del filtro de camareros cuando pulsas en él -->
                <button class="botones-ocultar" onclick="toggleFilter('camareroFilter')">Mostrar/Ocultar Filtro de Camareros</button>
            </div>
        </div>

        <div id="historial">
            <div class="filtros-separaciones">
                <div class="margen-1">
                    <div class="historial">
                        <h2 class="filtro-margin-top">Historial</h2>
                        <div class="margen-2-tercera">
                            <?php
                            try {
                                // Consulta para mostrar todas las ocupaciones con sus detalles
                                $sqlHistorial = "SELECT m.id_mesa, s.nombre AS nombre_sala, o.fecha_inicio, o.fecha_fin
                                FROM tbl_ocupacion o
                                JOIN tbl_mesa m ON o.id_mesa = m.id_mesa
                                JOIN tbl_sala s ON m.id_sala = s.id_sala
                                WHERE o.fecha_fin IS NOT NULL
                                ORDER BY o.fecha_inicio";

                                // Ejecutar la consulta
                                $resultHistorial =  mysqli_query($conn, $sqlHistorial);

                                // Verificar si se obtuvieron resultados de la consulta
                                if ($resultHistorial->num_rows > 0) {
                                    // Mostrar los resultados
                                    while ($row = $resultHistorial->fetch_assoc()) {
                                        echo "ID Mesa: " . $row["id_mesa"] . "<br>";
                                        echo "Sala: " . $row["nombre_sala"] . "<br>";
                                        echo "Fecha Inicio: " . $row["fecha_inicio"] . "<br>";
                                        echo "Fecha Fin: " . $row["fecha_fin"] . "<br>";
                                        echo "<br>";
                                    }
                                } else {
                                    echo "No se encontraron resultados";
                                }
                            } catch (Exception $e) {
                                echo "Error: " . $e->getMessage();
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filtros-separaciones">
                <div class="margen-1">
                    <h2 class="filtro-margin-top">Historial por fecha</h2>
                    <form action="mostrar_mesas.php" method="post" onsubmit="return validar_fecha()">
                        <input class="select-fecha" type="date" id="fecha" name="fechaFiltro">
                        <input class="aceptar-select-fecha" type="submit" value="Filtrar">
                        <span id="error_fecha"></span>
                    </form>
                    <div class="margen-2-cuarta">
                        <?php
                        // Si detecta la variable de sesión del filtro de fecha se llamará a la función correspondiente con el valor del input de id fecha
                        if (isset($_SESSION['fechaFiltro'])) {
                            echo "<div id='fechaFilter' class='visible'>";
                            filtrarMesasPorFecha($conn, $_SESSION['fechaFiltro']);
                            echo "</div>";
                        }
                        mysqli_close($conn);
                        ?>
                    </div>
                </div>
                <!-- Este botón muestra u oculta los resultados del filtro de fecha cuando pulsas en él -->
                <button class="botones-ocultar" onclick="toggleFilter('fechaFilter')">Mostrar/Ocultar Filtro por Fecha</button><br>
            </div>
        </div>
    </div>

</body>

</html>