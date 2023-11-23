<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body>
    <?php
    // Creamos la variable de errores que está vacía 
    $errores = "";
    session_start();

    // Verificar si el campo 'usuario' está presente en $_POST
    if (isset($_POST['usuario'])) {
        // Recogemos los datos que ha introducido el usuario
        $usuario = $_POST['usuario'];

        // Incluir el archivo de funciones
        require_once('./funciones.php');
        // Incluir el archivo de conexión a la base de datos
        include_once("./conexion.php");

        // Verificar si el usuario ya existe en la base de datos
        $sql_check = "SELECT nombre, contra FROM tbl_camarero WHERE nombre = ?";
        $stmt_check = mysqli_stmt_init($conn);

        mysqli_stmt_prepare($stmt_check, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $usuario);
        mysqli_stmt_execute($stmt_check);

        // Guardamos el resultado
        mysqli_stmt_store_result($stmt_check);

        // Verificamos si se encontró algún resultado
        if (mysqli_stmt_num_rows($stmt_check) === 0) {
            // El usuario no existe, agregar un mensaje de error a la variable $errores
            $errores = '?nombreNotExist=true';
        } else {
            // El usuario existe, ahora verificamos la contraseña
            $pwd = $_POST['pwd']; // Asegúrate de que estás recogiendo la contraseña del formulario
            $pwdEncriptada = hash("sha256", $pwd);

            // Consulta para obtener la contraseña almacenada en la base de datos
            $sql_password = "SELECT contra FROM tbl_camarero WHERE nombre = ?";
            $stmt_password = mysqli_stmt_init($conn);

            mysqli_stmt_prepare($stmt_password, $sql_password);
            mysqli_stmt_bind_param($stmt_password, "s", $usuario);
            mysqli_stmt_execute($stmt_password);
            mysqli_stmt_bind_result($stmt_password, $stored_password);
            mysqli_stmt_fetch($stmt_password);
            mysqli_stmt_close($stmt_password);

            // Verificar si la contraseña ingresada coincide con la almacenada en la base de datos
            if (hash_equals($pwdEncriptada, $stored_password)) {
                // Contraseña coincide, mostrar la alerta y redirigir a home.php
                 // Contraseña coincide, redirigir a sesiones.php
                 $datosRecibidos = array(
                    'usuario' => $usuario

                );
                $datosDevueltos = http_build_query($datosRecibidos);
                header("Location: ./sessiones.php?" .  $datosDevueltos);
                exit();
                    
                ?>
                <script>
                    function pasar() {
                        Swal.fire({
                            title: "Aceptado",
                            text: "Has entrado a la página principal",
                            icon: "success",
                        }).then(() => {
                       // Contraseña coincide, redirigir a sesiones.php
                        $datosRecibidos = array(
                            'usuario' => $usuario

                        );
                        $datosDevueltos = http_build_query($datosRecibidos);
                        header("Location: ./sessiones.php?" .  $datosDevueltos);
                        exit();
                            
                        });
                    }
                    document.addEventListener("DOMContentLoaded", function () {
                        pasar(); // Llama a la función pasar() que muestra la alerta
                    });
                </script>
                <?php
                // Evitar contenido HTML antes de la redirección
                exit();
            } else {
                // La contraseña no coincide, agregar un mensaje de error a la variable $errores
                $errores = '?passwdIncorrect=true';
            }

            // Si hay errores, redirigir a formulario.php con mensajes de error y datos de usuario
            if ($errores != "") {
                $datosRecibidos = array(
                    'usuario' => $usuario,
                );
                $datosDevueltos = http_build_query($datosRecibidos);
                header("Location: ./formulario.php" . $errores . "&" . $datosDevueltos);
                exit();
            }
        }
    }
    ?>
</body>

</html>
