<?php
/**
 * index-admin.php
 * Punto de entrada para administradores
 * Redirige al login del administrador
 */

// Cargar configuración
require_once __DIR__ . '/../config/config.php';

// Construir la ruta relativa correctamente
$baseUrl = rtrim(BASE_URL, '/');
$redirectUrl = $baseUrl . '/app/controllers/LoginController.php?action=login';

header("Location: $redirectUrl");
exit;
?>
