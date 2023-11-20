<?php
// Iniciamos sesion para poder trabajar con las variables $_SESSION
session_start();
// Destruir todas las variables de sesion
session_unset();

// Destruir la sesión
session_destroy();

header('location: ./login.php');
exit();