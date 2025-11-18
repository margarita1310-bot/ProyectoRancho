<?php
 /*
 * ReservaController.php
 * 
 * Controlador para gestión de reservas.
 * Permite listar, filtrar por fecha, confirmar (con asignación opcional de mesa) y cancelar reservas.
 * 
 * Acciones:
 * - index: GET - Muestra lista de reservas
 * - listar: GET - Retorna JSON con reservas (opcional filtro por fecha)
 * - confirmar: POST - Marca reserva como confirmada (opcionalmente asigna mesa)
 * - declinar: POST - Elimina una reserva
 * 
 * Tabla de base de datos: reserva (id_reserva, id_cliente, id_evento, id_mesa, fecha, hora, num_personas, estado, fecha_creacion, folio)	
)
 * 
 * Estados de reserva: 'pendiente', 'confirmada', 'cancelada'
 * 
 * Requiere: Autenticación de administrador (ensureAdmin())
 */

require_once __DIR__ . '/../models/ReservaModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

class ReservaController {
     /*
     * index()
     * Muestra la lista completa de reservas.
     * @return void - Incluye vista reservas.php
     */
    public function index() {
        // Cargar vista de reservas (dashboard incluirá esta vista)
        $resModel = new Reserva();
        $reserva = $resModel->getAll();
        require_once __DIR__ . '/../../app/views/admin/ReservaAdmin.php';
    }

     /*
     * listar()
     * Retorna lista de reservas en JSON.
     * Si se proporciona fecha, filtra por esa fecha.
     * @return void - Retorna JSON con array de reservas
     */
    public function listar() {
        header('Content-Type: application/json; charset=utf-8');
        $fecha = $_GET['fecha'] ?? null;
        $res = new Reserva();
        if ($fecha) { echo json_encode($res->getByDate($fecha)); return; }
        echo json_encode($res->getAll());
    }

     /*
     * confirmar()
     * Marca una reserva como confirmada.
     * Opcionalmente asigna un número de mesa a la reserva.
     * Flujo:
     * 1. Valida ID de reserva
     * 2. Si mesa se proporciona:
     *    - Valida que sea numérico
     *    - Actualiza: estado='confirmada', mesa={numero}
     * 3. Si mesa no se proporciona:
     *    - Actualiza solo: estado='confirmada'
     * @return void - Retorna JSON
     */
    public function confirmar() {
        header('Content-Type: application/json; charset=utf-8');
        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        if (!$id || !ctype_digit($id)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_id']); return; }
        $mesa = isset($_POST['mesa']) ? trim($_POST['mesa']) : null;
        $res = new ReservaModel();
        if ($mesa !== null && $mesa !== '') { 
            if (!ctype_digit($mesa)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'mesa_invalid']); return; }
            $ok = $res->confirm(intval($id), intval($mesa));
        } else {
            $ok = $res->confirm(intval($id));
        }
        header('Content-Type: application/json; charset=utf-8');
        echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo confirmar']);
    }

     /*
     * cancelar()
     * Elimina (declina) una reserva.
     * @return void - Retorna JSON
     */
    public function cancelar() {
        header('Content-Type: application/json; charset=utf-8');
        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        if (!$id || !ctype_digit($id)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_id']); return; }
        $res = new ReservaModel();
        $ok = $res->delete(intval($id));
        echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo eliminar reserva']);
    }
}

$controller = new ReservaController();
$action = $_GET['action'] ?? 'index';
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
        echo 'Acción no encontrada.';
    }
}
?>
