<?php
session_start();
include_once("../../inc/conexion.php");

//Comprobar si el usuario ha iniciado sesión
// if (!isset($_SESSION['id_camarero'])) {
//     header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
//     exit();
// }

// Función para mostrar las mesas ocupadas por los camareros que más mesas han ocupado
function mostrarCamarerosOrdenadosPorMesas($conn)
{
    try {
        // Consulta SQL para mostrar los camareros ordenados por la cantidad de mesas que han ocupado
        $sqlCamareros = "SELECT c.nombre as nombre_camarero, COUNT(o.id_mesa) as num_mesas_ocupadas,
            GROUP_CONCAT(o.id_mesa ORDER BY o.id_mesa) as mesas_ocupadas_ids,
            GROUP_CONCAT(o.num_veces_ocupada ORDER BY o.id_mesa) as veces_ocupada
            FROM tbl_camarero c
            LEFT JOIN (
                SELECT id_camarero, id_mesa, COUNT(*) as num_veces_ocupada
                FROM tbl_ocupacion
                GROUP BY id_camarero, id_mesa
            ) o ON c.id_camarero = o.id_camarero
            GROUP BY c.id_camarero
            ORDER BY num_mesas_ocupadas DESC";
        $stmtCamareros = mysqli_prepare($conn, $sqlCamareros);
        mysqli_stmt_execute($stmtCamareros);
        $resultCamareros = mysqli_stmt_get_result($stmtCamareros);
        if (!$resultCamareros) {
            die("Error en la consulta: " . mysqli_error($conn));
        }
        if ($resultCamareros->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($resultCamareros)) {
                echo "<p>Camarero: " . $row['nombre_camarero'] . " - Mesas Ocupadas: " . $row['num_mesas_ocupadas'] . "</p>";
                echo "<br>";
                echo "<p>Mesas Ocupadas:</p>";
                echo "<br>";
                $mesasIds = explode(",", $row['mesas_ocupadas_ids']);
                $vecesOcupada = explode(",", $row['veces_ocupada']);
                for ($i = 0; $i < count($mesasIds); $i++) {
                    echo "Mesa ID: " . $mesasIds[$i] . " - Veces Ocupada: " . $vecesOcupada[$i] . "<br>";
                }
            }
        } else {
            echo "<p>No hay resultados.</p>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

function filtrarMesasPorCapacidad($conn, $capacidadFiltro)
{
    try {
        // Consulta SQL para filtrar mesas por capacidad
        $sqlFiltro = "SELECT m.id_mesa, m.capacidad, s.nombre as sala_nombre 
FROM tbl_mesa m
INNER JOIN tbl_sala s ON m.id_sala = s.id_sala 
WHERE m.capacidad = $capacidadFiltro AND m.ocupada = 0 
ORDER BY m.capacidad";
        $resultFiltro = mysqli_query($conn, $sqlFiltro);
        echo "<br>";
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
        echo "<h3>Filtrado por fecha: $fechaFiltro</h3>";
        if ($resultFiltroFecha) {
            if (mysqli_num_rows($resultFiltroFecha) > 0) {

                while ($row = mysqli_fetch_assoc($resultFiltroFecha)) {
                    echo "ID Mesa: " . $row["id_mesa"] . "<br>";
                    echo "Sala: " . $row["nombre_sala"] . "<br>";
                    echo "Fecha Inicio: " . $row["fecha_inicio"] . "<br>";
                    echo "Fecha Fin: " . $row["fecha_fin"];
                }
            } else {
                echo "<br>";
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

<script>
    function toggleFilter(filterId) {
        var filter = document.getElementById(filterId);
        filter.classList.toggle("hidden");
        filter.classList.toggle("visible");
    }
</script>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/home.css">
</head>

<body>
    <header>
        <a href="../../index.php"><img id="logo" src="../../src/LOGO3.png" alt="logo"></a>
        <a href="./terraza1.php" class="secciones seleccionado-ahora">
            <p>Terrazas</p>
        </a>
        <a href="../comedores/comedor1.php" class="secciones">
            <p>Comedores</p>
        </a>
        <a href="../salas-privadas/sala-privada1.php" class="secciones">
            <p>Salas privadas</p>
        </a>
        <a href="" class="secciones">
            <p>Cerrar sesión</p>
        </a>
        <hr class="hr-header">
        <!-- TERRAZAS -->
        <div class="salas flex">
            <form method="post" action="./terraza1.php">
                <input type='submit' name='terraza_1' value="Terraza 1" class="secciones-secund seleccionado-ahora">
            </form>
            <form method="post" action="./terraza2.php">
                <input type='submit' name='terraza_2' value="Terraza 2" class="secciones-secund">
            </form>
            <form method="post" action="./terraza3.php">
                <input type='submit' name='terraza_3' value="Terraza 3" class="secciones-secund">
            </form>
            <form method="post" action="./terraza4.php">
                <input type='submit' name='terraza_4' value="Terraza 4" class="secciones-secund">
            </form>
        </div>
        <!-- --------- -->
    </header>
    <div class="row flex" id="">
        <div id="restaurante">
            <?php
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
            } elseif (isset($_POST['sala_1'])) {
                mostrarMesas('sala_1', $conn);
            } elseif (isset($_POST['sala_2'])) {
                mostrarMesas('sala_2', $conn);
            } elseif (isset($_POST['sala_3'])) {
                mostrarMesas('sala_3', $conn);
            } elseif (isset($_POST['sala_4'])) {
                mostrarMesas('sala_4', $conn);
            } else {
                // Redirigir o manejar de alguna manera si se accede a esta página de manera incorrecta
                header("Location: ../../index.php");
                exit();
            }
            ?>
        </div>

        <div id="filtro">
            <div class="margen">
                <h2>Mesas Disponibles</h2>
                <p>(Filtradas por capacidad X personas)</p>
                <form action="./terraza1.php" method="post">
                    <select name="capacidadFiltro" id="" class="select-personas">
                        <option disabled selected>Seleccione opcion</option>
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
                <div class="visible" id="capacidadFilter">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['capacidadFiltro'])) {
                        $capacidadFiltro = $_POST['capacidadFiltro'];
                        filtrarMesasPorCapacidad($conn, $capacidadFiltro);
                    }
                    ?>
                </div>
            </div>
            <button onclick="toggleFilter('capacidadFilter')">Mostrar/Ocultar Filtro de Capacidad</button>

            <div class="margen">
                <h2>Camareros (Ordenados por la cantidad de mesas ocupadas)</h2>
                <br>
                <div class="visible" id="camareroFilter">
                    <?php
                    // Luego, puedes llamar a esta función según sea necesario.
                    mostrarCamarerosOrdenadosPorMesas($conn);
                    ?>
                </div>
            </div>
            <button onclick="toggleFilter('camareroFilter')">Mostrar/Ocultar Filtro de Camareros</button><br>
        </div>

        <div id="historial">
            <div class="margen">
                <div class="historial">
                    <h2>Historial</h2>
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
                                echo "<hr class='hr-historial'>";
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
            <div class="margen">
                <h2>Historial por fecha</h2>
                <form action="./terraza1.php" method="post">
                    <label for="fechaFiltro">Filtrar por fecha:</label>
                    <input type="date" name="fechaFiltro">
                    <input type="submit" value="Filtrar">
                </form>
                <div class="visible" id="fechaFilter">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fechaFiltro'])) {
                        $fechaFiltro = $_POST['fechaFiltro'];
                        filtrarMesasPorFecha($conn, $fechaFiltro);
                    }
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
            <br>
            <button onclick="toggleFilter('fechaFilter')">Mostrar/Ocultar Filtro por Fecha</button><br>
        </div>
    </div>
</body>

</html>
