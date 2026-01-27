<?php

$servidor = "localhost";
$baseDatos = "tower";
$usuario = "root";
$contrasenia = "";

try {
    $conexionBD = new PDO("mysql:host=$servidor;dbname=$baseDatos;charset=utf8", $usuario, $contrasenia);
    //echo "conectado";
} catch (Exception $error) {
    echo $error->getMessage();
}

?>