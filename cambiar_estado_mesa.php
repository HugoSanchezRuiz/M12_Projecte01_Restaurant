<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<style>
    html {
        background-color: #3a5f68;
        color: #3a5f68;
    }
</style>

<?php
session_start();

include_once("./inc/conexion.php");

if (isset($_POST['mesa_id'])) {
    $mesa_id = mysqli_real_escape_string($conn, $_POST['mesa_id']);

    // Antes de la transacción
    echo "Antes de la transacción";

    try {
        // Inicia la transacción
        mysqli_autocommit($conn, false);
        mysqli_begin_transaction($conn);

        // Consulta SQL para obtener el estado actual de ocupación de la mesa
        $sqlEstadoActual = "SELECT ocupada FROM tbl_mesa WHERE id_mesa = ?";
        $stmtEstadoActual = mysqli_prepare($conn, $sqlEstadoActual);
        mysqli_stmt_bind_param($stmtEstadoActual, "i", $mesa_id);
        mysqli_stmt_execute($stmtEstadoActual);
        $resultEstadoActual = mysqli_stmt_get_result($stmtEstadoActual);

        if ($resultEstadoActual) {
            $row = mysqli_fetch_assoc($resultEstadoActual);
            $ocupada = $row['ocupada'];

            // Invierte el estado de ocupación
            $nuevoEstado = !$ocupada;

            // Actualiza el estado de ocupación en la base de datos
            $sqlActualizarEstado = "UPDATE tbl_mesa SET ocupada = ? WHERE id_mesa = ?";
            $stmtActualizarEstado = mysqli_prepare($conn, $sqlActualizarEstado);
            mysqli_stmt_bind_param($stmtActualizarEstado, "ii", $nuevoEstado, $mesa_id);
            $resultActualizarEstado = mysqli_stmt_execute($stmtActualizarEstado);

            if ($resultActualizarEstado) {
                // Si la mesa está ocupada, inserta una nueva fila en tbl_ocupacion con la fecha de inicio
                if ($nuevoEstado == 1) {
                    $id_camarero = 1; // Reemplaza con el ID del camarero actual
                    $sqlInsertarOcupacion = "INSERT INTO tbl_ocupacion (id_mesa, id_camarero, fecha_inicio, fecha_fin) VALUES (?, ?, NOW(), NULL)";
                    $stmtInsertarOcupacion = mysqli_prepare($conn, $sqlInsertarOcupacion);
                    mysqli_stmt_bind_param($stmtInsertarOcupacion, "ii", $mesa_id, $id_camarero);
                    $resultInsertarOcupacion = mysqli_stmt_execute($stmtInsertarOcupacion);

                    if (!$resultInsertarOcupacion) {
                        // Si hay un error en la inserción, realiza un rollback
                        mysqli_rollback($conn);
                        echo "Error al insertar la ocupación de la mesa: " . mysqli_error($conn);
                        exit();
                    }
                } else {
                    // Si la mesa está desocupada, actualiza la fecha_fin en tbl_ocupacion
                    $sqlActualizarOcupacion = "UPDATE tbl_ocupacion SET fecha_fin = NOW() WHERE id_mesa = ? AND fecha_fin IS NULL";
                    $stmtActualizarOcupacion = mysqli_prepare($conn, $sqlActualizarOcupacion);
                    mysqli_stmt_bind_param($stmtActualizarOcupacion, "i", $mesa_id);
                    $resultActualizarOcupacion = mysqli_stmt_execute($stmtActualizarOcupacion);

                    if (!$resultActualizarOcupacion) {
                        // Si hay un error en la actualización, realiza un rollback
                        mysqli_rollback($conn);
                        echo "Error al actualizar la ocupación de la mesa: " . mysqli_error($conn);
                        exit();
                    }
                }

                // Confirma la transacción
                mysqli_commit($conn);

                // Cierra la conexión
                mysqli_close($conn);

                // Agrega el script de SweetAlert para la notificación y redirección
                echo "
    <script>
        Swal.fire({
            title: 'Aceptado',
            text: 'Has entrado a la página principal',
            icon: 'success'
        }).then(() => {
            const usuario = '" . $usuarioRecibido . "';
            window.location.href = './mostrar_mesas.php?usuario=' + usuario;
        });
    </script>
";
            } else {
                // Si hay un error en la actualización, realiza un rollback (deshace todos los cambios hechos)
                mysqli_rollback($conn);
                echo "Error al actualizar el estado de la mesa: " . mysqli_error($conn);
            }
        } else {
            echo "Error al obtener el estado actual de la mesa: " . mysqli_error($conn);
        }

        // Cierra la conexión
        mysqli_close($conn);

        // Redirige de nuevo a la página anterior
        // header("Location: ./mostrar_mesas.php");
        // exit();
        // Después de la transacción
        echo "Después de la transacción";
    } catch (Exception $e) {
        // Manejo de excepciones
        echo "Excepción: " . $e->getMessage();
    }
} else {
    // Si se intenta acceder a este archivo de manera incorrecta, redirige a la página principal
    // header("Location: ./index.php");
    echo "cambio no realizado";
    exit();
}
?>
