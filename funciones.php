<?php
$error="";
//creamos  la funcion para controlar camposVacios
function validaCampoVacio($campo) {
    if(empty($campo)){
        $error= true; //Hay un error
    }else{
        $error= false; //No hay un error
    }
    return $error;//devuelve el error
}
