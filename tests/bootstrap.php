<?php
/**
 * Bootstrap para PHPUnit
 * Inicializa el autoloader y configuraciones necesarias para tests
 */

// Cargar autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');

// Definir constantes útiles para tests
define('TEST_DIR', __DIR__);
define('APP_DIR', dirname(__DIR__) . '/app');
define('PUBLIC_DIR', dirname(__DIR__) . '/public');

// Variables de entorno para BD de pruebas (pueden sobrescribirse en phpunit.xml)
if (!getenv('DB_HOST')) putenv('DB_HOST=localhost');
if (!getenv('DB_NAME')) putenv('DB_NAME=lajoya_gestion_test');
if (!getenv('DB_USER')) putenv('DB_USER=root');
if (!getenv('DB_PASS')) putenv('DB_PASS=rancho');

echo "\n=== Bootstrap PHPUnit cargado ===\n";
echo "Base de datos de pruebas: " . getenv('DB_NAME') . "\n\n";
