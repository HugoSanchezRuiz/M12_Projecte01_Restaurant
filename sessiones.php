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
    session_start();

    if (!isset($_GET['usuario'])) {
        header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
        exit();
    } else {
        $usuarioRecibido = $_GET['usuario'];
        $_SESSION['usuario'] = $usuarioRecibido;
        ?>
        <script>
            Swal.fire({
                title: "Aceptado",
                text: "Has entrado a la página principal",
                icon: "success"
            }).then(() => {
                // Contraseña coincide, redirigir a home.php
                const usuario = "<?php echo $usuarioRecibido; ?>";
                window.location.href = `./home.php?usuario=${usuario}`;
            });
        </script>
        <?php
        exit(); // Salir después de enviar el script de JavaScript
    }
    ?>
</body>

</html>
