<?php
session_start();

//Verificar si el usuario ha iniciando sesión
if (!isset($_SESSION['admin'])) {
    header("Location: ../../app/controllers/LoginController.php?action=login");
    exit;
}

class AdminController {
    public function dashboard() {
        include '../../app/views/admin/dashboard.php';
    }

    public function logout() {
        session_destroy();
        header("Location: ../../app/controllers/LoginController.php?action=login");
        exit;
    }
}

//Ejecución automática del controlador
$controller = new AdminController();
$action = $_GET['action'] ?? 'dashboard';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "Acción no encontrada.";
}
?>