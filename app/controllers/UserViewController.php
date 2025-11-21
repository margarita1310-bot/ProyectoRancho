<?php
 /*
 * UserViewController.php
 * 
 * Controlador para mostrar vistas del área de usuario.
 * Punto de entrada principal para el sitio público.
 * 
 * NO requiere autenticación (público)
 */

class UserViewController {
    
    /*
     * index()
     * Muestra el dashboard principal del usuario.
     * @return void - Incluye vista DashboardUser.php
     */
    public function index() {
        require_once __DIR__ . '/../views/user/DashboardUser.php';
    }
}

// Enrutamiento
$controller = new UserViewController();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    // Por defecto, mostrar index
    $controller->index();
}
?>
