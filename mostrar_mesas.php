<link rel="stylesheet" href="style.css">
<style>
.salas {
    display: none;
}

.visible {
   display: block !important;
}
</style>
<?php
session_start();
include_once("./conexion.php");

// Comprobar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
    exit();
}
?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
   <script>
   $(document).ready(function () {
       $(".terraza").on("click", function(){
        $(".terraza_opciones").toggleClass("visible");
       });
   });
    </script>
<header>
        <a href="./home.php"><img id="logo" src="./img/LOGO3.png" alt="logo"></a>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_1' value="terraza_1" class="terraza secciones seleccionado-ahora">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='comedor_1' value="comedor_1" class="comedor secciones">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='sala_privada_1' value="sala_privada_1" class=" sala secciones">
        </form>
        <a href="" class="secciones">
            <p>Cerrar sesión</p>
        </a>
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
function mostrarMesas($nombreSala, $conn) {
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
            echo "<h2>Mesas de $nombreSala</h2>";
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
}elseif (isset($_POST['comedor_1'])) {
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
}  else {
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


//Comprobar si el usuario ha iniciado sesión
// if (!isset($_SESSION['usuario'])) {
//     header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
//     exit();
// }

// Función para mostrar las mesas ocupadas por los camareros que más mesas han ocupado
function mostrarCamarerosOrdenadosPorMesas($conn){
    try {
        // Consulta SQL para mostrar los camareros ordenados por la cantidad de mesas que han ocupado
        $sqlCamareros = "SELECT
        c.nombre as nombre_camarero,
        COUNT(o.id_mesa) as num_mesas_ocupadas,
        GROUP_CONCAT(o.id_mesa ORDER BY o.id_mesa) as mesas_ocupadas_ids,
        GROUP_CONCAT(o.num_veces_ocupada ORDER BY o.id_mesa) as veces_ocupada,
        GROUP_CONCAT(DISTINCT o.fecha_inicio ORDER BY o.id_mesa) as fechas_inicio
    FROM tbl_camarero c
    LEFT JOIN (
        SELECT id_camarero, id_mesa, COUNT(*) as num_veces_ocupada, fecha_inicio as fecha_inicio
        FROM tbl_ocupacion o
        GROUP BY id_camarero, id_mesa
    ) o ON c.id_camarero = o.id_camarero
    GROUP BY c.id_camarero
    ORDER BY num_mesas_ocupadas DESC;";

        $stmtCamareros = mysqli_prepare($conn, $sqlCamareros);
        mysqli_stmt_execute($stmtCamareros);
        $resultCamareros = mysqli_stmt_get_result($stmtCamareros);

        if (!$resultCamareros) {
            die("Error en la consulta: " . mysqli_error($conn));
        }

        if ($resultCamareros->num_rows > 0) {
            echo "<h2>Camareros (Ordenados por la cantidad de mesas ocupadas)</h2>";
            while ($row = mysqli_fetch_assoc($resultCamareros)) {
                echo "<p>------------------------</p>";
                echo "<p>Camarero: " . $row['nombre_camarero'] . " - Mesas Ocupadas: " . $row['num_mesas_ocupadas'] . "</p>";
                echo "<p>Mesas Ocupadas:</p>";

                $mesasIds = explode(",", $row['mesas_ocupadas_ids']);
                $vecesOcupada = explode(",", $row['veces_ocupada']);
                $fechasInicio = explode(",", $row['fechas_inicio']);

                for ($i = 0; $i < count($mesasIds); $i++) {
                    echo "Mesa ID: " . $mesasIds[$i] . " - Veces Ocupada: " . $vecesOcupada[$i];

                    // Si la mesa se ha ocupado más de una vez, mostrar las fechas correspondientes
                    if ($vecesOcupada[$i] > 1) {
                        echo "<ul>";
                        for ($j = 0; $j < $vecesOcupada[$i]; $j++) {
                            echo "<li>Ocupación " . ($j + 1) . ": " . $fechasInicio[$i] . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo " - Fecha Inicio: " . $fechasInicio[$i];
                        echo "<br>";
                    }

                    echo "<br>";
                }
                echo "<p>------------------------</p>";
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

        echo "<br>";
        echo "<h2>Mesas Disponibles (Filtradas por capacidad: $capacidadFiltro personas)</h2>";
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
        echo "<h2>Historial (Filtrado por fecha: $fechaFiltro)</h2>";
        if ($resultFiltroFecha) {
            if (mysqli_num_rows($resultFiltroFecha) > 0) {

                while ($row = mysqli_fetch_assoc($resultFiltroFecha)) {
                    echo "ID Mesa: " . $row["id_mesa"] . "<br>";
                    echo "Sala: " . $row["nombre_sala"] . "<br>";
                    echo "Fecha Inicio: " . $row["fecha_inicio"] . "<br>";
                    echo "Fecha Fin: " . $row["fecha_fin"] . "<br>";
                    echo "<br>";
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



<!DOCTYPE html>
<html>

<head>
    <title>Lista de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        .hidden {
            display: none;
        }

        .visible {
            display: block;
        }
    </style>
    <script>
        function toggleFilter(filterId) {
            var filter = document.getElementById(filterId);
            filter.classList.toggle("hidden");
            filter.classList.toggle("visible");
        }
    </script>
</head>
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

    <button onclick="toggleFilter('camareroFilter')">Mostrar/Ocultar Filtro de Camareros</button><br>
    <?php

    echo "<br>";

    if (isset($_SESSION['capacidadFiltro'])) {
        //$capacidadFiltro = $_POST['capacidadFiltro'];

        echo "<div id='capacidadFilter' class='visible'>";

        filtrarMesasPorCapacidad($conn, $_SESSION['capacidadFiltro']);
        echo "</div>";
    }
    ?>

    <label for="filtro">Filtro de Mesas</label>
    <form action="mostrar_mesas.php" method="post">
        <select name="capacidadFiltro">
            <option disabled selected>Selecciona opción</option>
            <option value="2">2 personas</option>
            <option value="3">3 personas</option>
            <option value="4">4 personas</option>
            <option value="6">6 personas</option>
            <option value="8">8 personas</option>
            <option value="10">10 personas</option>
            <option value="15">15 personas</option>
        </select>
        <input type="submit" value="Enviar">
    </form><br>
    <br>

    <button onclick="toggleFilter('capacidadFilter')">Mostrar/Ocultar Filtro de Capacidad</button>

    <?php

    if (isset($_SESSION['fechaFiltro'])) {
        //$fechaFiltro = $_POST['fechaFiltro'];

        echo "<div id='fechaFilter' class='visible'>";
        filtrarMesasPorFecha($conn, $_SESSION['fechaFiltro']);
        echo "</div>";
    }
    ?>
    <br>
    <br>
    <form action="mostrar_mesas.php" method="post" onsubmit="return validar_fecha()">
        <label for="fechaFiltro">Filtrar por fecha:</label>
        <input type="date" id="fecha" name="fechaFiltro">
        <input type="submit" value="Filtrar">
        <br>

        <span id="error_fecha"></span>
    </form><br>
    <br>

    <button onclick="toggleFilter('fechaFilter')">Mostrar/Ocultar Filtro por Fecha</button><br>
    <br>



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

        // Cerrar la conexión a la base de datos
        mysqli_close($conn);
        ?>
    </div>
