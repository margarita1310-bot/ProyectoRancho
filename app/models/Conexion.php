<?php
class Conexion {
    private static $conexion = null;
    public static function conectar() {
        if (self::$conexion === null) {
            try {
                $host = 'localhost';
                $usuario = 'root';
                $contraseña = 'rancho';
                $base_de_datos = 'lajoya_gestion';

                self::$conexion = new PDO("mysql:host=$host;dbname=$base_de_datos;charset=utf8", $usuario, $contraseña);
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return self::$conexion;
    }
}
?>