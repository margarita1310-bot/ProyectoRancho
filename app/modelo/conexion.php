<?php
//Conexion
$host = 'localhost';
$usuario = 'root';
$contraseña = 'rancho';
$base_de_datos = 'lajoya_gestion';
$puerto = '3306';

$conex = mysqli_connect($host, $usuario, $contraseña, $base_de_datos, $puerto);

if($conex->connect_error){
    echo "Error en la conexion: " . $conex->connect_error;
}
?>