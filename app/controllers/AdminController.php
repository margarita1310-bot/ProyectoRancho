<?php

require_once __DIR__ . '/Auth.php';

ensureAdmin();

/**
 * Controlador de administración principal
 * Gestiona el dashboard y acciones administrativas del sistema
 */
class AdminController
{
    /**
     * Carga el dashboard administrativo con datos de promociones, eventos, productos, reservas, disponibilidades y mesas.
     * 
     * @return void Renderiza la vista del dashboard de administración
     */
    public function dashboard()
    {
        require_once __DIR__ . '/../models/PromocionModel.php';
        require_once __DIR__ . '/../models/EventoModel.php';
        require_once __DIR__ . '/../models/ProductoModel.php';
        require_once __DIR__ . '/../models/ReservaModel.php';
        require_once __DIR__ . '/../models/DisponibilidadModel.php';
        require_once __DIR__ . '/../models/MesaModel.php';

        // Obtener datos de promociones
        $promModel = new PromocionModel();
        $promocion = $promModel->getPromocionesConProductos();

        // Obtener datos de eventos
        $evModel = new EventoModel();
        $evento = $evModel->getAll();

        // Obtener datos de productos
        $productoModel = new ProductoModel();
        $producto = $productoModel->getAll();

        // Obtener datos de reservas
        $reservaModel = new ReservaModel();
        $reserva = $reservaModel->getAll();

        // Obtener datos de disponibilidades
        $disponibilidadModel = new DisponibilidadModel();
        $disponibilidad = $disponibilidadModel->getAll();

        // Obtener datos de mesas activas
        $mesaModel = new MesaModel();
        $mesas = $mesaModel->getMesasActivas();

        // Cargar vista del dashboard
        include __DIR__ . '/../views/admin/DashboardAdmin.php';
    }

    /**
     * Obtiene estadísticas del sistema en formato JSON.
     * Retorna conteos de: promociones activas, eventos próximos, reservas pendientes y mesas disponibles.
     * 
     * @return void Envía respuesta JSON con estadísticas o error
     */
    public function getEstadisticas()
    {
        header('Content-Type: application/json; charset=utf-8');

        require_once __DIR__ . '/../models/PromocionModel.php';
        require_once __DIR__ . '/../models/EventoModel.php';
        require_once __DIR__ . '/../models/ReservaModel.php';
        require_once __DIR__ . '/../models/MesaModel.php';

        try {
            // Contar promociones disponibles
            $promModel = new PromocionModel();
            $promociones = $promModel->getAll();
            $promosActivas = count(array_filter($promociones, function ($p) {
                return isset($p['estado']) && $p['estado'] === 'Disponible';
            }));

            // Contar eventos próximos
            $evModel = new EventoModel();
            $eventos = $evModel->getAll();
            $eventosProximos = count(array_filter($eventos, function ($e) {
                return isset($e['fecha']) && strtotime($e['fecha']) >= strtotime(date('Y-m-d'));
            }));

            // Contar reservas pendientes
            $reservaModel = new ReservaModel();
            $reservas = $reservaModel->getAll();
            $reservasPendientes = count(array_filter($reservas, function ($r) {
                return isset($r['estado']) && strtolower($r['estado']) === 'pendiente';
            }));

            // Contar mesas disponibles
            $mesaModel = new MesaModel();
            $mesas = $mesaModel->getMesasActivas();
            $mesasDisponibles = count(array_filter($mesas, function ($m) {
                return isset($m['estado']) && $m['estado'] === 'Disponible';
            }));

            // Retornar estadísticas
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

    /**
     * Obtiene los datos del perfil del administrador autenticado.
     * Valida la sesión y retorna información del admin en formato JSON.
     * 
     * @return void Envía datos del admin o error de autenticación/base de datos
     */
    public function getPerfil()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Validar que el admin está autenticado
        if (!isset($_SESSION['admin']) || !isset($_SESSION['admin']['id_admin'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'unauthorized']);
            return;
        }

        require_once __DIR__ . '/../models/Conexion.php';

        try {
            // Conectar a la base de datos
            $db = Conexion::conectar();

            // Obtener datos del administrador
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

    /**
     * Cierra la sesión del administrador y redirige al login.
     * Destruye todos los datos de sesión y redirige a la página de inicio de sesión.
     * 
     * @return void Redirige y termina la ejecución
     */
    public function logout()
    {
        session_destroy();
        header("Location: ../../app/controllers/LoginController.php?action=login");
        exit;
    }
}

// Instanciar el controlador y ejecutar la acción solicitada
$controller = new AdminController();
$action = $_GET['action'] ?? 'dashboard';

if (method_exists($controller, $action)) {
    // Si el método existe, ejecutarlo
    $controller->$action();
} else {
    // Verificar si es una solicitud AJAX
    $isAjax = false;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $isAjax = true;
    }
    if (!$isAjax && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        $isAjax = true;
    }

    // Retornar error apropiado según el tipo de solicitud
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'action_not_found']);
    } else {
        echo "Acción no encontrada.";
    }
}
?>