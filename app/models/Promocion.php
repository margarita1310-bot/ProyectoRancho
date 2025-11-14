<?php
require_once '/app/models/Conexion.php';

class Promocion {
    private $conexion;

    public function __construct() {
        $this->conexion = (new Database())->connect();
    }

    //Crear una nueva promoción
    public function crear($nombre, $descripcion, $fecha_inicio, $fecha_fin, $imagen) {
        
    }
}