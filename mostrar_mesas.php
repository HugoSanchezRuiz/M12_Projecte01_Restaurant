<?php
session_start();
include_once("./inc/conexion.php");

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
        // Consulta SQL para mostrar los camareros ordenados por la cantidad de mesas que han ocupado
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


        $stmtCamareros = mysqli_prepare($conn, $sqlCamareros);
        mysqli_stmt_execute($stmtCamareros);
        $resultCamareros = mysqli_stmt_get_result($stmtCamareros);

        if (!$resultCamareros) {
            die("Error en la consulta: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($resultCamareros) > 0) {
            echo "<h2>Camareros (Ordenados por la cantidad de mesas ocupadas)</h2>";
            while ($row = mysqli_fetch_assoc($resultCamareros)) {
                echo "<p>------------------------</p>";
                echo "<p>Camarero: " . $row['nombre_camarero'] . " - Mesas Ocupadas: " . $row['num_mesas_ocupadas'] . "</p>";
                
                if ($row['num_mesas_ocupadas'] > 0) {
                    echo "<p>Mesas Ocupadas:</p>";
                    
                    $mesasIds = explode(",", $row['mesas_ocupadas_ids']);
                    $vecesOcupada = explode(",", $row['veces_ocupada']);
                    $fechasInicio = explode(",", $row['fechas_inicio']);

                    $totalVecesOcupadas = 0;

                    for ($i = 0; $i < count($mesasIds); $i++) {
                        if ($mesasIds[$i] != null && $vecesOcupada[$i] != null) {
                            echo "Mesa " . $mesasIds[$i] . " - Veces Ocupada: " . $vecesOcupada[$i];
                            $totalVecesOcupadas += $vecesOcupada[$i];
                            
                            // Si la mesa se ha ocupado más de una vez, mostrar las fechas correspondientes
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
        // Consulta SQL para filtrar mesas por capacidad
        $sqlFiltro = "SELECT m.id_mesa, m.capacidad, s.nombre as sala_nombre 
        FROM tbl_mesa m 
        INNER JOIN tbl_sala s ON m.id_sala = s.id_sala 
        WHERE m.capacidad = ? AND m.ocupada = 0 
        ORDER BY m.capacidad";

        $stmtFiltro = mysqli_prepare($conn, $sqlFiltro);
        mysqli_stmt_bind_param($stmtFiltro, "i", $capacidadFiltro);
        mysqli_stmt_execute($stmtFiltro);
        $resultFiltro = mysqli_stmt_get_result($stmtFiltro);

        echo "<h2>Filtradas por capacidad: $capacidadFiltro personas</h2>";
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


// verificamos que el formulario ha sido enviado por post y si contiene un campo llamado capacidadFiltro

function filtrarMesasPorFecha($conn, $fechaFiltro)
{
    try {
        // Consulta SQL para filtrar mesas por fecha
        $sqlFiltroFecha = "SELECT m.id_mesa, s.nombre AS nombre_sala, o.fecha_inicio, o.fecha_fin
            FROM tbl_ocupacion o
            JOIN tbl_mesa m ON o.id_mesa = m.id_mesa
            JOIN tbl_sala s ON m.id_sala = s.id_sala
            WHERE  o.fecha_fin IS NOT NULL AND DATE (o.fecha_inicio) = ?
            ORDER BY o.fecha_inicio";

        $stmtFiltroFecha = mysqli_prepare($conn, $sqlFiltroFecha);
        mysqli_stmt_bind_param($stmtFiltroFecha, "s", $fechaFiltro);
        mysqli_stmt_execute($stmtFiltroFecha);
        $resultFiltroFecha = mysqli_stmt_get_result($stmtFiltroFecha);

        echo "<br>";
        echo "<h4>$fechaFiltro</h4>";
        if ($resultFiltroFecha) {
            if (mysqli_num_rows($resultFiltroFecha) > 0) {

                while ($row = mysqli_fetch_assoc($resultFiltroFecha)) {
                    echo "ID Mesa: " . $row["id_mesa"] . "<br>";
                    echo "Sala: " . $row["nombre_sala"] . "<br>";
                    echo "Fecha Inicio: " . $row["fecha_inicio"] . "<br>";
                    echo "Fecha Fin: " . $row["fecha_fin"] . "<br>";
                }
            } else {
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
                try {
                    mysqli_autocommit($conn, false);
                    mysqli_begin_transaction($conn);

                    // $sqlSala = "SELECT COUNT(id_mesa) AS num_mesas FROM tbl_mesa 
                    // WHERE id_sala = (SELECT id_sala FROM tbl_sala WHERE nombre = '$nombreSala')";
                    // -- Consulta para mostrar las mesas de una sala específica
                    $sqlSala = "SELECT
            -- COUNT(ms.id_mesa)
            ms.id_mesa,
            ms.capacidad,
            ms.ocupada
        FROM
            tbl_mesa ms
        JOIN
            tbl_sala sl ON ms.id_sala = sl.id_sala
        WHERE
            sl.nombre = '$nombreSala'";

                    // Ejecutar la consulta
                    $resultSala = mysqli_query($conn, $sqlSala);

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


                    // Confirmar la transacción
                    mysqli_commit($conn);

                    // Cerrar la conexión a la base de datos
                    // mysqli_close($conn);
                } catch (Exception $e) {
                    // Deshacemos la actualización en caso de que se genere alguna excepción
                    mysqli_rollback($conn);
                    echo "Error: " . $e->getMessage();
                }
            }



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
                // Redirigir o manejar de alguna manera si se accede a esta página de manera incorrecta
                // header("Location: ./home.php");
                // header("Location: mostrar_mesas.php");

                // exit();
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
                    <div class="margen-2-primera">
                        <div class="visible" id="capacidadFilter">
                            <?php

                            if (isset($_SESSION['capacidadFiltro'])) {
                                //$capacidadFiltro = $_POST['capacidadFiltro'];

                                echo "<div id='capacidadFilter' class='visible'>";

                                filtrarMesasPorCapacidad($conn, $_SESSION['capacidadFiltro']);
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
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

                        echo "<div id='camareroFilter' class='visible'>";
                        mostrarCamarerosOrdenadosPorMesas($conn);
                        echo "</div>";
                        ?>
                    </div>
                </div>
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
                                $sqlHistorial = "SELECT
            m.id_mesa,
            s.nombre AS nombre_sala,
            o.fecha_inicio,
            o.fecha_fin
        FROM
            tbl_ocupacion o
            JOIN tbl_mesa m ON o.id_mesa = m.id_mesa
            JOIN tbl_sala s ON m.id_sala = s.id_sala
        WHERE
            o.fecha_fin IS NOT NULL
        ORDER BY
            o.fecha_inicio";

                                // Ejecutar la consulta
                                $resultHistorial =  mysqli_query($conn, $sqlHistorial);

                                // Verificar si se obtuvieron resultados
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

                        if (isset($_SESSION['fechaFiltro'])) {
                            //$fechaFiltro = $_POST['fechaFiltro'];

                            echo "<div id='fechaFilter' class='visible'>";
                            filtrarMesasPorFecha($conn, $_SESSION['fechaFiltro']);
                            echo "</div>";
                        }
                        mysqli_close($conn);
                        ?>
                    </div>
                </div>
                <button class="botones-ocultar" onclick="toggleFilter('fechaFilter')">Mostrar/Ocultar Filtro por Fecha</button><br>
            </div>
        </div>
    </div>

</body>

</html>