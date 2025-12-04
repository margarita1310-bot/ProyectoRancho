<?php
/**
 * app/helpers/ConfigValidator.php
 * Valida la configuración del ambiente
 */

class ConfigValidator
{
    /**
     * Verifica que todas las extensiones PHP necesarias estén cargadas
     * 
     * @return array Array de extensiones faltantes
     */
    public static function checkExtensions()
    {
        $required = ['pdo', 'pdo_mysql', 'json', 'session'];
        $missing = [];
        
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }
        
        return $missing;
    }
    
    /**
     * Verifica que la base de datos sea accesible
     * 
     * @return bool True si la conexión es exitosa
     */
    public static function checkDatabase()
    {
        try {
            require_once dirname(__DIR__) . '/models/Conexion.php';
            $pdo = Conexion::conectar();
            return $pdo instanceof PDO;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Verifica que el archivo .env exista
     * 
     * @return bool
     */
    public static function checkEnv()
    {
        return file_exists(dirname(dirname(__DIR__)) . '/.env');
    }
    
    /**
     * Verifica que el directorio de carga de archivos sea escribible
     * 
     * @return bool
     */
    public static function checkWritableDirectories()
    {
        $directories = [
            dirname(dirname(__DIR__)) . '/public/images/evento',
            dirname(dirname(__DIR__)) . '/public/images/promocion'
        ];
        
        foreach ($directories as $dir) {
            if (is_dir($dir) && !is_writable($dir)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Genera reporte completo de configuración
     * 
     * @return array Array con todos los chequeos
     */
    public static function getReport()
    {
        return [
            'php_version' => phpversion(),
            'php_version_ok' => version_compare(phpversion(), '7.4', '>='),
            'extensions_ok' => empty(self::checkExtensions()),
            'missing_extensions' => self::checkExtensions(),
            'database_ok' => self::checkDatabase(),
            'env_file_ok' => self::checkEnv(),
            'writable_dirs_ok' => self::checkWritableDirectories(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'base_url' => defined('BASE_URL') ? BASE_URL : 'Not defined'
        ];
    }
}
