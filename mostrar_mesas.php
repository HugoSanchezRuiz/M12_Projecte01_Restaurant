<?php
session_start();
include_once("./conexion.php");

// Comprobar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_camarero'])) {
    header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
    exit();
}

function mostrarMesas($nombreSala, $conn) {
    try {
        mysqli_autocommit($conn, false);
        mysqli_begin_transaction($conn);

        // Consulta SQL para mostrar las mesas de la terraza con información de ocupación
        $sqlSala = "SELECT m.*, s.nombre as nombre_sala, o.fecha_inicio, o.fecha_fin
            FROM tbl_mesa m
            JOIN tbl_sala s ON m.id_sala = s.id_sala
            LEFT JOIN tbl_ocupacion o ON m.id_mesa = o.id_mesa
            WHERE s.nombre='$nombreSala'";

        // Ejecutar la consulta
        $resultSala = mysqli_query($conn, $sqlSala);

        if ($resultSala) {
            echo "<h2>Mesas de $nombreSala</h2>";
            echo "<form method='post' action='cambiar_estado_mesa.php'>";
            while ($row = mysqli_fetch_assoc($resultSala)) {
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
} elseif (isset($_POST['terraza_4'])) {
    mostrarMesas('terraza_4', $conn);
}elseif (isset($_POST['comedor_1'])) {
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
}  else {
    // Redirigir o manejar de alguna manera si se accede a esta página de manera incorrecta
    header("Location: ./home.php");
    exit();
}
?>