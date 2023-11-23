
 <?php
session_start();

if (!isset($_GET['usuario'])) {
    header('Location: ./formulario.php'); // Redirige a la página de inicio de sesión
    exit();
} else {
    $usuarioRecibido = $_GET['usuario'];
    $_SESSION['usuario'] = $usuarioRecibido;
    header('Location: ./home.php'); // Redirige a la página de inicio
    exit();
}
?>
