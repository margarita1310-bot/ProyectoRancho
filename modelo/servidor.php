<?php
//Conexion
$conex = mysqli_connect('localhost', 'root', 'rancho', 'lajoya_gestion', '3306');
 if($conex){
    echo "Conexion exitosa";
 } else {
    echo "Error en la conexion";
 }
?>