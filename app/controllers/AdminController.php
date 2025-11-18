<?php
 /*
 * AdminController.php
 * Controlador principal del dashboard administrativo.
 * Maneja acciones de administración general y carga datos de todos los módulos.
 * Acciones:
 * - dashboard: Carga el dashboard principal con productos, promociones y eventos
 * - logout: Cierra la sesión del administrador y redirige a login
 * Requiere: Autenticación de administrador (ensureAdmin())
 */

require_once __DIR__ . '/Auth.php';
ensureAdmin();

class AdminController {
     /*
     * dashboard()
     * Carga el dashboard principal del administrador con todos los datos.
     * @return void - Incluye vista dashboard.php
     */
    public function dashboard() {
        // Cargar datos para las vistas incluidas en el dashboard
        require_once __DIR__ . '/../models/PromocionModel.php';
        require_once __DIR__ . '/../models/EventoModel.php';
        require_once __DIR__ . '/../models/ProductoModel.php';
        
        $promModel = new PromocionModel();
        $promocion = $promModel->getAll();
        
        $evModel = new EventoModel();
        $evento = $evModel->getAll();

        $productoModel = new ProductoModel();
        $producto = $productoModel->getAll();


        include '../../app/views/admin/DashboardAdmin.php';
    }

     /*
     * logout()
     * Cierra la sesión activa del administrador.
     * @return void - Redirige a LoginController
     */
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
    $isAjax = false;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') $isAjax = true;
    if (!$isAjax && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) $isAjax = true;
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'action_not_found']);
    } else {
        echo "Acción no encontrada.";
    }
}
?>