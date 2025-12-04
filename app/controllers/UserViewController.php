<?php

/**
 * Controlador de Vista de Usuario
 * Gestiona la renderización de vistas para usuarios públicos
 */
class UserViewController
{
    /**
     * Carga y renderiza el dashboard del usuario.
     * Muestra la interfaz principal para que los usuarios realicen reservas.
     * 
     * @return void Renderiza la vista del dashboard de usuario
     */
    public function index()
    {
        require_once __DIR__ . '/../views/user/DashboardUser.php';
    }
}

// Instanciar el controlador y ejecutar la acción solicitada
$controller = new UserViewController();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    // Si la acción no existe, mostrar el index por defecto
    $controller->index();
}
?>