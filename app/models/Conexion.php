<?php

/**
 * Clase de Conexión a Base de Datos
 * Gestiona la conexión singleton a la base de datos MySQL usando PDO
 * Compatible con diferentes entornos: localhost, Infinity Free, etc.
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
                // Cargar configuración desde el archivo config.php
                if (!defined('DB_HOST')) {
                    require_once dirname(__DIR__) . '/../config/config.php';
                }

                // Usar las constantes de configuración
                $host = DB_HOST;
                $usuario = DB_USER;
                $contraseña = DB_PASS;
                $base_de_datos = DB_NAME;

                // Crear conexión PDO con soporte UTF-8 y configuraciones para Infinity Free
                $dsn = "mysql:host=$host;dbname=$base_de_datos;charset=utf8mb4";
                self::$conexion = new PDO(
                    $dsn,
                    $usuario,
                    $contraseña,
                    array(
                        PDO::ATTR_PERSISTENT => false,
                        PDO::ATTR_TIMEOUT => 5,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    )
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