<?php
/**
 * index-user.php
 * Punto de entrada para usuarios
 * Redirige al dashboard de usuario
 */

// Cargar configuración
require_once __DIR__ . '/../config/config.php';

// Construir la ruta relativa correctamente
$baseUrl = rtrim(BASE_URL, '/');
$redirectUrl = $baseUrl . '/app/controllers/UserViewController.php';

header("Location: $redirectUrl");
exit;
?>