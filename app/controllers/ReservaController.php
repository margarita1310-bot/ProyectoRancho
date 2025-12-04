<?php

require_once __DIR__ . '/../models/ReservaModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

/**
 * Controlador de Reservas
 * Gestiona la confirmación, cancelación y obtención de reservas
 * Incluye gestión de disponibilidad de mesas por fecha
 */
class ReservaController
{
    /**
     * Carga la vista del dashboard administrativo con todas las reservas.
     * 
     * @return void Renderiza la vista del dashboard
     */
    public function index()
    {
        $resModel = new ReservaModel();
        $reserva = $resModel->getAll();
        require_once __DIR__ . '/../views/admin/DashboardAdmin.php';
    }

    /**
     * Lista reservas en formato JSON.
     * Si se proporciona fecha, retorna reservas de esa fecha.
     * Si no, retorna todas las reservas.
     * 
     * @return void Envía respuesta JSON con reservas
     */
    public function listar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $fecha = $_GET['fecha'] ?? null;
        $res = new ReservaModel();

        if ($fecha) {
            echo json_encode($res->getByDate($fecha));
            return;
        }

        echo json_encode($res->getAll());
    }

    /**
     * Confirma una reserva pendiente y opcionalmente asigna una mesa.
     * Valida que la mesa esté activa y disponible antes de asignarla.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function confirmar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_POST['id']) ? trim($_POST['id']) : null;

        // Validar ID de reserva
        if (!$id || !ctype_digit($id)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'missing_id']);
            return;
        }

        // Obtener reserva
        $res = new ReservaModel();
        $reserva = $res->getById(intval($id));

        if (!$reserva) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'reservation_not_found']);
            return;
        }

        // Verificar que la reserva esté pendiente
        if ($reserva['estado'] !== 'pendiente') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'reservation_not_pending', 'detail' => 'La reserva ya fue procesada']);
            return;
        }

        // Obtener y validar mesa si se proporciona
        $idMesa = isset($_POST['id_mesa']) ? trim($_POST['id_mesa']) : null;

        if ($idMesa !== null && $idMesa !== '') {
            // Validar ID de mesa
            if (!ctype_digit($idMesa)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'mesa_invalid']);
                return;
            }

            require_once __DIR__ . '/../models/MesaModel.php';
            $mesaModel = new MesaModel();
            $mesa = $mesaModel->getMesaById(intval($idMesa));

            // Verificar que la mesa existe y está activa
            if (!$mesa || !$mesa['activa']) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'mesa_not_active', 'detail' => 'La mesa no está activa']);
                return;
            }

            // Verificar que la mesa no esté ocupada
            if ($mesa['estado'] === 'Ocupada') {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'mesa_occupied', 'detail' => 'La mesa ya está Ocupada']);
                return;
            }

            $ok = $res->confirm(intval($id), intval($idMesa));
        } else {
            $ok = $res->confirm(intval($id));
        }

        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo confirmar']);
    }

    /**
     * Cancela una reserva existente.
     * Valida que la reserva no esté ya cancelada.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function cancelar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_POST['id']) ? trim($_POST['id']) : null;

        // Validar ID
        if (!$id || !ctype_digit($id)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'missing_id']);
            return;
        }

        // Obtener reserva
        $res = new ReservaModel();
        $reserva = $res->getById(intval($id));

        if (!$reserva) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'reservation_not_found']);
            return;
        }

        // Verificar que no esté ya cancelada
        if ($reserva['estado'] === 'cancelada') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'already_cancelled', 'detail' => 'La reserva ya fue cancelada']);
            return;
        }

        // Eliminar reserva
        $ok = $res->delete(intval($id));
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo eliminar reserva']);
    }

    /**
     * Obtiene las mesas activas y disponibles para una fecha específica.
     * Valida que la fecha sea proporcionada.
     * 
     * @return void Envía respuesta JSON con mesas disponibles o error
     */
    public function obtenerMesasDisponiblesPorFecha()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $fecha = $_GET['fecha'] ?? null;

            // Validar fecha
            if (!$fecha) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'missing_fecha']);
                return;
            }

            error_log("Obteniendo mesas disponibles para fecha: $fecha");

            // Obtener mesas disponibles
            $res = new ReservaModel();
            $mesasDisponibles = $res->getMesasActivasYDisponibles($fecha);

            error_log("Mesas disponibles encontradas: " . count($mesasDisponibles));

            echo json_encode($mesasDisponibles);
        } catch (Exception $e) {
            error_log("Error en obtenerMesasDisponiblesPorFecha: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'exception', 'detail' => $e->getMessage()]);
        }
    }

    /**
     * Obtiene todas las reservas de una fecha específica con información de mesas.
     * Incluye detalles de mesa asignada para cada reserva.
     * 
     * @return void Envía respuesta JSON con reservas y mesas o error
     */
    public function obtenerReservasPorFecha()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $fecha = $_GET['fecha'] ?? null;

            // Validar fecha
            if (!$fecha) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'missing_fecha']);
                return;
            }

            error_log("Obteniendo reservas para fecha: $fecha");

            // Obtener reservas con información de mesas
            $res = new ReservaModel();
            $resultado = $res->getReservasPorFechaConMesas($fecha);

            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en obtenerReservasPorFecha: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'exception', 'detail' => $e->getMessage()]);
        }
    }
}

// Instanciar el controlador y ejecutar la acción solicitada
$controller = new ReservaController();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
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
        echo 'Acción no encontrada.';
    }
}
?>