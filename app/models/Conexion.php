<?php

/**
 * Clase de Conexión a Base de Datos
 * Gestiona la conexión singleton a la base de datos MySQL usando PDO
 */
class Conexion
{
    /**
     * Instancia estática de la conexión (patrón Singleton)
     * 
     * @var PDO|null
     */
    private static $conexion = null;

    /**
     * Obtiene la conexión a la base de datos.
     * Si no existe conexión activa, la crea. Si ya existe, retorna la existente.
     * Implementa el patrón Singleton para asegurar una única conexión.
     * 
     * @return PDO Conexión a la base de datos
     */
    public static function conectar()
    {
        if (self::$conexion === null) {
            try {
                // Configuración de la base de datos
                $host = 'localhost';
                $usuario = 'root';
                $contraseña = 'rancho';
                $base_de_datos = 'lajoya_gestion';

                // Crear conexión PDO con soporte UTF-8
                self::$conexion = new PDO(
                    "mysql:host=$host;dbname=$base_de_datos;charset=utf8",
                    $usuario,
                    $contraseña
                );

                // Configurar modo de error para excepciones
                self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }

        return self::$conexion;
    }
}
?>