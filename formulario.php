<!DOCTYPE html>
<html>
<head>
    <title>Formulario de Datos Personales</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<style>

.error-container {
        color: red;
        font-size: 14px;
        margin-bottom: 10px;
      }

</style>
<body>
    <h1>Formulario Iniciar Sesión</h1>
     <script src="./validaciones.js"></script> 
     
    <form action="./proc_formulario.php" method="post"onsubmit="return validar()" >
        
        

        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario">
        <br>
        <span id="error_usuario"></span>
     
        <br>

        <label for="pwd">Contraseña:</label>
        <input type="password" id="pwd" name="pwd">
        <br>
        <span id="error_pwd"></span>
        <div class="error-container">
        <?php if (isset($_GET['nombreNotExist'])) { echo "El usuario o la contraseña esta incorrecto"; } ?>
        <?php if (isset($_GET['passwdIncorrect'])) { echo "El usuario o la contraseña esta incorrecto"; } ?>
        <br>
        </div>

        <input type="submit" name="enviar" value="Enviar">

        
    </form>

</body>
</html>

