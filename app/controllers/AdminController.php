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
        require_once __DIR__ . '/../models/ReservaModel.php';
        require_once __DIR__ . '/../models/DisponibilidadModel.php';
        require_once __DIR__ . '/../models/MesaModel.php';
        
        $promModel = new PromocionModel();
        $promocion = $promModel->getAll();
        
        $evModel = new EventoModel();
        $evento = $evModel->getAll();

        $productoModel = new ProductoModel();
        $producto = $productoModel->getAll();

        $reservaModel = new ReservaModel();
        $reserva = $reservaModel->getAll();

        $disponibilidadModel = new DisponibilidadModel();
        $disponibilidad = $disponibilidadModel->getAll();

        $mesaModel = new MesaModel();
        $mesas = $mesaModel->getMesasActivas();

        include __DIR__ . '/../views/admin/DashboardAdmin.php';
    }

     /*
     * getEstadisticas()
     * 
     * Retorna estadísticas para el dashboard en formato JSON.
     * Cuenta promociones activas, eventos próximos, reservas pendientes y mesas disponibles.
     * 
     * @return void - Retorna JSON
     */
    public function getEstadisticas() {
        header('Content-Type: application/json; charset=utf-8');
        
        require_once __DIR__ . '/../models/PromocionModel.php';
        require_once __DIR__ . '/../models/EventoModel.php';
        require_once __DIR__ . '/../models/ReservaModel.php';
        require_once __DIR__ . '/../models/MesaModel.php';
        
        try {
            // Contar promociones disponibles
            $promModel = new PromocionModel();
            $promociones = $promModel->getAll();
            $promosActivas = count(array_filter($promociones, function($p) {
                return isset($p['estado']) && $p['estado'] === 'Disponible';
            }));
            
            // Contar eventos próximos (desde hoy en adelante)
            $evModel = new EventoModel();
            $eventos = $evModel->getAll();
            $eventosProximos = count(array_filter($eventos, function($e) {
                return isset($e['fecha']) && strtotime($e['fecha']) >= strtotime(date('Y-m-d'));
            }));
            
            // Contar reservas pendientes
            $reservaModel = new ReservaModel();
            $reservas = $reservaModel->getAll();
            $reservasPendientes = count(array_filter($reservas, function($r) {
                return isset($r['estado']) && strtolower($r['estado']) === 'pendiente';
            }));
            
            // Contar mesas disponibles
            $mesaModel = new MesaModel();
            $mesas = $mesaModel->getMesasActivas();
            $mesasDisponibles = count(array_filter($mesas, function($m) {
                return isset($m['estado']) && $m['estado'] === 'Disponible';
            }));
            
            echo json_encode([
                'status' => 'ok',
                'promociones' => $promosActivas,
                'eventos' => $eventosProximos,
                'reservas' => $reservasPendientes,
                'mesas' => $mesasDisponibles
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'db_error']);
        }
    }

     /*
     * getPerfil()
     * 
     * Retorna los datos del administrador logueado en formato JSON.
     * 
     * @return void - Retorna JSON con nombre y correo del admin
     */
    public function getPerfil() {
        header('Content-Type: application/json; charset=utf-8');
        
        if (!isset($_SESSION['admin']) || !isset($_SESSION['admin']['id_admin'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'unauthorized']);
            return;
        }
        
        require_once __DIR__ . '/../models/Conexion.php';
        
        try {
            $db = Conexion::conectar();
            $stmt = $db->prepare("SELECT id_admin, nombre, correo FROM administrador WHERE id_admin = ?");
            $stmt->execute([$_SESSION['admin']['id_admin']]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin) {
                echo json_encode([
                    'status' => 'ok',
                    'admin' => $admin
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'not_found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'db_error']);
        }
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