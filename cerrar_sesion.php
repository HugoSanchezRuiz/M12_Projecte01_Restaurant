<?php
// Iniciamos sesion para poder trabajar con las variables $_SESSION
session_start();
// Destruir todas las variables de sesion
session_unset();

// Destruir la sesión
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Sesión</title>
    <!-- Agrega la librería de SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>

<script>
// Muestra la alerta al destruir la sesión
Swal.fire({
    title: "Sesión cerrada",
    text: "Has cerrado sesión exitosamente",
    icon: "success",
    showConfirmButton: false, // Ocultar el botón de confirmación
}).then(() => {
    // Redirigir a login.php después de la espera
    window.location.href = './login.php';
});
</script>

</body>
</html>

<?php
exit();
?>
