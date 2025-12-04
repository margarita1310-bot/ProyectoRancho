<?php
/**
 * config/config.php
 * Configuración centralizada del aplicativo
 * Soporta variables de entorno para diferentes entornos (local, producción, Infinity Free)
 */

// Definir la zona horaria
date_default_timezone_set('America/Bogota');

// ============= CONFIGURACIÓN DE BASE DE DATOS =============
// Detectar si se están usando variables de entorno (.env)
$envFile = __DIR__ . '/../.env';
$envVariables = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $envVariables[trim($key)] = trim($value);
        }
    }
}

// Configuración de BD con valores por defecto y soporte para variables de entorno
define('DB_HOST', getenv('DB_HOST') ?: $envVariables['DB_HOST'] ?? 'localhost');
define('DB_USER', getenv('DB_USER') ?: $envVariables['DB_USER'] ?? 'root');
define('DB_PASS', getenv('DB_PASS') ?: $envVariables['DB_PASS'] ?? 'rancho');
define('DB_NAME', getenv('DB_NAME') ?: $envVariables['DB_NAME'] ?? 'lajoya_gestion');

// ============= CONFIGURACIÓN DE URLS =============
// Detectar automáticamente la URL base según el entorno
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');

// Si estamos en un subdirectorio, incluirlo en la URL base
if ($scriptPath !== '/' && $scriptPath !== '\\') {
    define('BASE_URL', $protocol . $host . $scriptPath . '/');
} else {
    define('BASE_URL', $protocol . $host . '/');
}

// ============= CONFIGURACIÓN DE RUTAS =============
// Directorio raíz del proyecto
define('PROJECT_ROOT', dirname(__DIR__));
define('APP_ROOT', PROJECT_ROOT . '/app');
define('VIEWS_ROOT', APP_ROOT . '/views');
define('MODELS_ROOT', APP_ROOT . '/models');
define('CONTROLLERS_ROOT', APP_ROOT . '/controllers');
define('HELPERS_ROOT', APP_ROOT . '/helpers');
define('PUBLIC_ROOT', PROJECT_ROOT . '/public');

// ============= CONFIGURACIÓN DE DEBUGGING =============
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || $envVariables['DEBUG_MODE'] === 'true' ?? false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// ============= CARGAR AUTOLOADER =============
require_once PROJECT_ROOT . '/vendor/autoload.php';

?>
