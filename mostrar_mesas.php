<?php
session_start();

include_once("./conexion.php");

function mostrarMesas($nombreSala, $conn) {
    try {
        mysqli_autocommit($conn, false);
        mysqli_begin_transaction($conn);

        // Consulta SQL para mostrar las mesas de la terraza con información de ocupación
        $sqlTerraza = "SELECT m.*, s.nombre as nombre_sala, o.fecha_inicio, o.fecha_fin
            FROM tbl_mesa m
            JOIN tbl_sala s ON m.id_sala = s.id_sala
            LEFT JOIN tbl_ocupacion o ON m.id_mesa = o.id_mesa
            WHERE s.nombre='$nombreSala'";

        // Ejecutar la consulta
        $resultTerraza = mysqli_query($conn, $sqlTerraza);

        if ($resultTerraza) {
            echo "<h2>Mesas de $nombreSala</h2>";
            echo "<form method='post' action='cambiar_estado_mesa.php'>";
            while ($row = mysqli_fetch_assoc($resultTerraza)) {
                echo "<button type='submit' name='mesa_id' value='" . $row['id_mesa'] . "' ";
                
                if ($row['ocupada']) {
                    echo "class='ocupada'";
                } else {
                    echo "class='no-ocupada'";
                }

                echo ">Mesa " . $row['id_mesa'] . " - Capacidad: " . $row['capacidad'];

                echo "</button>";
            }
            echo "</form>";
        } else {
            echo "Error en la consulta: " . mysqli_error($conn);
        }

        // Confirmar la transacción
        mysqli_commit($conn);

        // Cerrar la conexión a la base de datos
        mysqli_close($conn);
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
} elseif (isset($_POST['comedor_1'])) {
    mostrarMesas('comedor_1', $conn);
} elseif (isset($_POST['comedor_2'])) {
    mostrarMesas('comedor_2', $conn);
} elseif (isset($_POST['sala_1'])) {
    mostrarMesas('sala_1', $conn);
} elseif (isset($_POST['sala_2'])) {
    mostrarMesas('sala_2', $conn);
} elseif (isset($_POST['sala_3'])) {
    mostrarMesas('sala_3', $conn);
} else {
    // Redirigir o manejar de alguna manera si se accede a esta página de manera incorrecta
    header("Location: ./home.php");
    exit();
}
?>