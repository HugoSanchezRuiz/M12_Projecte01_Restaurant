<?php
session_start();
include_once("./conexion.php");

//Comprobar si el usuario ha iniciado sesión
// if (!isset($_SESSION['id_camarero'])) {
//     header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
//     exit();
// }

// Función para mostrar las mesas ocupadas por los camareros que más mesas han ocupado
function mostrarCamarerosOrdenadosPorMesas($conn) {
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
            echo "<h2>Camareros (Ordenados por la cantidad de mesas ocupadas)</h2>";
            echo "<br>";
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

// Luego, puedes llamar a esta función según sea necesario.
mostrarCamarerosOrdenadosPorMesas($conn);

function filtrarMesasPorCapacidad($conn, $capacidadFiltro) {
    try {
        // Consulta SQL para filtrar mesas por capacidad
        $sqlFiltro = "SELECT m.id_mesa, m.capacidad, s.nombre as sala_nombre 
        FROM tbl_mesa m 
        INNER JOIN tbl_sala s ON m.id_sala = s.id_sala 
        WHERE m.capacidad = $capacidadFiltro AND m.ocupada = 0 
        ORDER BY m.capacidad";

        $resultFiltro = mysqli_query($conn, $sqlFiltro);
        
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['capacidadFiltro'])) {
  $capacidadFiltro = $_POST['capacidadFiltro'];
  
  filtrarMesasPorCapacidad($conn, $capacidadFiltro);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fechaFiltro'])) {
    $fechaFiltro = $_POST['fechaFiltro'];
    
    filtrarMesasPorFecha($conn, $fechaFiltro);
  }
  
  function filtrarMesasPorFecha($conn, $fechaFiltro) {
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
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light ">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="home_pendientes.php">Terraza</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="home_bloqueados.php">Comedor</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="home_bloqueados.php">Sala privada</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="cerrar_sesion.php">Cerrar sesión</a>
      </li>
    </ul>
  </div>
</nav>


<label for="filtro">Filtro de Mesas</label>
<form action="home.php" method="post">
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
</form>

<form action="home.php" method="post">
  <label for="fechaFiltro">Filtrar por fecha:</label>
  <input type="date" name="fechaFiltro">
  <input type="submit" value="Filtrar">
</form>

<!-- mostramos las mesas que hay en la terraza -->
<div class='terraza'>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_1' value="terraza_1">
        </form>

        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_2' value="terraza_2">
        </form>

        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_3' value="terraza_3">
        </form>

        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='terraza_4' value="terraza_4">
        </form>
</div>
<br>
<br>
<br>
<br>
<!-- mostramos las mesas que hay en el comedor -->
<div class='comedor'>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='comedor_1' value="comedor_1">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='comedor_2' value="comedor_2">
        </form>
        <form method="post" action="mostrar_mesas.php">
            <input type='submit' name='comedor_3' value="comedor_3">
        </form>
</div>
<br>
<br>
<br>
<br>

<!-- mostramos las mesas que hay en la sala privada -->
<div class='sala-privada'>
    <form method="post" action="mostrar_mesas.php">
        <input type='submit' name='sala_1' value="sala_1">
    </form>
    <form method="post" action="mostrar_mesas.php">
        <input type='submit' name='sala_2' value="sala_2">
    </form>
    <form method="post" action="mostrar_mesas.php">
        <input type='submit' name='sala_3' value="sala_3">
    </form>
    <form method="post" action="mostrar_mesas.php">
        <input type='submit' name='sala_4' value="sala_4">
    </form>
</div>

<div class="historial">
<h2>Historial</h2>
<?php
try{
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


</body>
</html>
</body>
</html> 