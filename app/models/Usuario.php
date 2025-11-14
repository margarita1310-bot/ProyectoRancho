<?php
require_once 'Conexion.php';

class Usuario {
    public static function verificar($correo, $password) {
        $db = Conexion::conectar();

        $query = $db->prepare("SELECT * FROM administrador where correo = :correo AND password = :password");
        $query->bindParam(":correo", $correo);
        $query->bindParam(":password", $password);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }
}
?>