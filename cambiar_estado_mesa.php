<?php
session_start();

include_once("./conexion.php");

if (isset($_POST['mesa_id'])) {
    $mesa_id = $_POST['mesa_id'];

    try {
        // Inicia la transacción
        mysqli_autocommit($conn, false);
        mysqli_begin_transaction($conn);

        // Consulta SQL para obtener el estado actual de ocupación de la mesa
        $sqlEstadoActual = "SELECT ocupada FROM tbl_mesa WHERE id_mesa = $mesa_id";
        $resultEstadoActual = mysqli_query($conn, $sqlEstadoActual);

        if ($resultEstadoActual) {
            $row = mysqli_fetch_assoc($resultEstadoActual);
            $ocupada = $row['ocupada'];

            // Invierte el estado de ocupación
            $nuevoEstado = !$ocupada;           

            // Actualiza el estado de ocupación en la base de datos
            $sqlActualizarEstado = "UPDATE tbl_mesa SET ocupada = '$nuevoEstado' WHERE id_mesa = $mesa_id";
            $resultActualizarEstado = mysqli_query($conn, $sqlActualizarEstado);

            if ($resultActualizarEstado) {
                // Si la mesa está ocupada, inserta una nueva fila en tbl_ocupacion con la fecha de inicio
                if ($nuevoEstado == 1) {
                    $id_camarero = 1; // Reemplaza con el ID del camarero actual
                    $sqlInsertarOcupacion = "INSERT INTO tbl_ocupacion (id_mesa, id_camarero, fecha_inicio, fecha_fin) VALUES ($mesa_id, $id_camarero, NOW(), NULL)";
                    $resultInsertarOcupacion = mysqli_query($conn, $sqlInsertarOcupacion);

                    if (!$resultInsertarOcupacion) {
                        // Si hay un error en la inserción, realiza un rollback
                        mysqli_rollback($conn);
                        echo "Error al insertar la ocupación de la mesa: " . mysqli_error($conn);
                        exit();
                    }
                } else {
                    // Si la mesa está desocupada, actualiza la fecha_fin en tbl_ocupacion
                    $sqlActualizarOcupacion = "UPDATE tbl_ocupacion SET fecha_fin = NOW() WHERE id_mesa = $mesa_id AND fecha_fin IS NULL";
                    $resultActualizarOcupacion = mysqli_query($conn, $sqlActualizarOcupacion);

                    if (!$resultActualizarOcupacion) {
                        // Si hay un error en la actualización, realiza un rollback
                        mysqli_rollback($conn);
                        echo "Error al actualizar la ocupación de la mesa: " . mysqli_error($conn);
                        exit();
                    }
                }

                // Confirma la transacción
                mysqli_commit($conn);
            } else {
                // Si hay un error en la actualización, realiza un rollback (deshace todos lo cambios hechos)
                mysqli_rollback($conn);
                echo "Error al actualizar el estado de la mesa: " . mysqli_error($conn);
            }
        } else {
            echo "Error al obtener el estado actual de la mesa: " . mysqli_error($conn);
        }

        // Cierra la conexión
        mysqli_close($conn);

        // Redirige de nuevo a la página anterior
        header("Location: ./home.php");
        exit();
    } catch (Exception $e) {
        // Si hay alguna excepción, realiza un rollback
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
} else {
    // Si se intenta acceder a este archivo de manera incorrecta, redirige a la página principal
    // header("Location: ./home.php");
    echo "cambio no realizado";
    exit();
}
?>
