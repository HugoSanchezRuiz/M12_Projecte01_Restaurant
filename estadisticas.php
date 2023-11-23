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
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Mesas</title>
    <!-- <link rel="stylesheet" href="./style.css"> -->

    <style>

table {
    border-collapse: collapse;
    width: 50%;
    margin: 20px;
}

th, td {
    border: 1px solid black;
    padding: 8px;
    text-align: left;
    color:red;
}

th {
    background-color: #540606;
}
    </style>
</head>
<body>
    <h1>Estadísticas de Mesas</h1>

    <h2>Terraza más ocupada</h2>
    <?php
    if ($rowsTerraza) {
        echo "<table>
                <tr>
                    <th>ID Mesa</th>
                    <th>Ocupaciones</th>
                </tr>";
        foreach ($rowsTerraza as $row) {
            echo "<tr>
                    <td>{$row['id_mesa']}</td>
                    <td>{$row['ocupaciones']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "No hay mesas ocupadas en la terraza.";
    }
    ?>

    <h2>Comedor más ocupado</h2>
    <?php
    if ($rowsComedor) {
        echo "<table>
                <tr>
                    <th>ID Mesa</th>
                    <th>Ocupaciones</th>
                </tr>";
        foreach ($rowsComedor as $row) {
            echo "<tr>
                    <td>{$row['id_mesa']}</td>
                    <td>{$row['ocupaciones']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "No hay mesas ocupadas en el comedor.";
    }
    ?>

    <h2>Sala Privada más ocupada</h2>
    <?php
    if ($rowsPrivada) {
        echo "<table>
                <tr>
                    <th>ID Mesa</th>
                    <th>Ocupaciones</th>
                </tr>";
        foreach ($rowsPrivada as $row) {
            echo "<tr>
                    <td>{$row['id_mesa']}</td>
                    <td>{$row['ocupaciones']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "No hay mesas ocupadas en la sala privada.";
    }

    // horas más ocupadas

    ?>

    <h2>Hora terraza más ocupada</h2>
    <?php
    if ($rowsHoras) {
        echo "<table>
                <tr>
                    <th>Hora</th>
                    <th>Ocupaciones</th>
                </tr>";
        foreach ($rowsHoras as $row) {
            echo "<tr>
                    <td>{$row['hora']}</td>
                    <td>{$row['ocupaciones']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "No hay datos disponibles para la terraza por horas.";
    }
    ?>
</body>
</html>
