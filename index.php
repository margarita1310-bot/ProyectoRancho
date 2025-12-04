<?php
/**
 * index.php
 * Punto de entrada principal del aplicativo
 * Compatible con Infinity Free y hosting compartido
 */

require_once __DIR__ . '/config/config.php';

// Detectar la ruta solicitada
$request = $_SERVER['REQUEST_URI'] ?? '/';
$basePath = dirname($_SERVER['SCRIPT_NAME']);

// Remover la ruta base si existe
if ($basePath !== '/') {
    $request = str_replace($basePath, '', $request);
}

// Remover parámetros de consulta
$request = strtok($request, '?');

// Normalizar la ruta
$request = '/' . trim($request, '/');

// Redireccionar según la ruta
switch ($request) {
    case '/':
    case '/admin':
    case '/admin/':
        header('Location: ' . BASE_URL . 'app/controllers/LoginController.php?action=login');
        exit;
        
    case '/user':
    case '/user/':
        header('Location: ' . BASE_URL . 'app/controllers/UserViewController.php');
        exit;
        
    default:
        // Si es un archivo o directorio real, permitir acceso
        if (is_file(__DIR__ . $request) || is_dir(__DIR__ . $request)) {
            return false;
        }
        
        // Si no existe, mostrar error 404
        http_response_code(404);
        echo "Página no encontrada: " . htmlspecialchars($request);
        exit;
}
