<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$dbserver= "localhost";
$dbusername="root";
$dbpassword="";
$dbbasedatos="db_restaurante";

try{
    $conn = @mysqli_connect($dbserver, $dbusername, $dbpassword, $dbbasedatos);


} catch (Exception $e){
    echo "Error en la conexión con la base de datos: " . $e->getMessage();
    die();
}