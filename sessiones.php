<?php
if (!isset($_GET['usuario'])) {
    header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
    exit();
   
} else{
    header('Location: ./home.php'); // Redirige a la página de inicio de sesión
    $usuarioRecibido = $_GET['usuario'];
    $_SESSION['usuario'] = $usuarioRecibido;
}
?>