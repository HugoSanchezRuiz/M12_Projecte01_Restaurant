<!DOCTYPE html>
<html>
<head>
    <title>Lista de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light ">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="home_pendientes.php">Terraza</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="home_bloqueados.php">Comedor</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="home_bloqueados.php">Sala privada</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="cerrar_sesion.php">Cerrar sesión</a>
      </li>
    </ul>
  </div>
</nav>

<?php
// inicia la sesion
session_start();

// Comprobar si el usuario ha iniciado sesión
// if (!isset($_SESSION['id_camarero'])) {
//     header('Location: ./login.php'); // Redirige a la página de inicio de sesión
//     exit();
// }

include_once("./conexion.php");


?>
<!-- mostramos las mesas que hay en la terraza -->
<div class='terraza'>
        <form method="post" action="mostrarMesas.php">
            <input type='submit' name='terraza_1' value="terraza_1">
        </form>

        <form method="post" action="mostrarMesas.php">
            <input type='submit' name='terraza_2' value="terraza_2">
        </form>

        <form method="post" action="mostrarMesas.php">
            <input type='submit' name='terraza_3' value="terraza_3">
        </form>
</div>
<br>
<br>
<br>
<br>
<!-- mostramos las mesas que hay en el comedor -->
<div class='comedor'>
        <form method="post" action="mostrarMesas.php">
            <input type='submit' name='comedor_1' value="comedor_1">
        </form>
        <form method="post" action="mostrarMesas.php">
            <input type='submit' name='comedor_2' value="comedor_2">
        </form>
</div>
<br>
<br>
<br>
<br>

<!-- mostramos las mesas que hay en la sala privada -->
<div class='sala-privada'>
    <form method="post" action="mostrarMesas.php">
        <input type='submit' name='sala_1' value="sala_1">
    </form>
    <form method="post" action="mostrarMesas.php">
        <input type='submit' name='sala_2' value="sala_2">
    </form>
    <form method="post" action="mostrarMesas.php">
        <input type='submit' name='sala_3' value="sala_3">
    </form>
    <form method="post" action="mostrarMesas.php">
        <input type='submit' name='sala_4' value="sala_4">
    </form>
</div>
<br>
<br>
<br>
<br>


</body>
</html>
</body>
</html> 