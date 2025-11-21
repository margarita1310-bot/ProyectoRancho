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
        require_once __DIR__ . '/../views/admin/DashboardAdmin.php';
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
     * Opcionalmente asigna un ID de mesa a la reserva y actualiza su estado.
     * 
     * Validaciones:
     * - La reserva debe existir
     * - La reserva debe estar en estado 'pendiente'
     * - Si se asigna mesa, esta debe estar disponible
     * 
     * Flujo:
     * 1. Valida ID de reserva
     * 2. Verifica que la reserva esté pendiente
     * 3. Si se proporciona id_mesa:
     *    - Valida que sea numérico
     *    - Verifica que la mesa esté Disponible
     *    - Actualiza: estado='confirmada', id_mesa={id}
     *    - Actualiza mesa: estado='Ocupada', id_cliente={cliente}
     * 4. Si no se proporciona mesa:
     *    - Actualiza solo: estado='confirmada'
     * 
     * @return void - Retorna JSON
     */
    public function confirmar() {
        header('Content-Type: application/json; charset=utf-8');
        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        
        if (!$id || !ctype_digit($id)) { 
            http_response_code(400); 
            echo json_encode(['status'=>'error','message'=>'missing_id']); 
            return; 
        }
        
        $res = new ReservaModel();
        
        // Verificar que la reserva existe y está pendiente
        $reserva = $res->getById(intval($id));
        if (!$reserva) {
            http_response_code(404);
            echo json_encode(['status'=>'error','message'=>'reservation_not_found']);
            return;
        }
        
        if ($reserva['estado'] !== 'pendiente') {
            http_response_code(400);
            echo json_encode(['status'=>'error','message'=>'reservation_not_pending', 'detail'=>'La reserva ya fue procesada']);
            return;
        }
        
        $idMesa = isset($_POST['id_mesa']) ? trim($_POST['id_mesa']) : null;
        
        if ($idMesa !== null && $idMesa !== '') { 
            if (!ctype_digit($idMesa)) { 
                http_response_code(400); 
                echo json_encode(['status'=>'error','message'=>'mesa_invalid']); 
                return; 
            }
            
            // Verificar que la mesa esté disponible
            require_once __DIR__ . '/../models/MesaModel.php';
            $mesaModel = new MesaModel();
            $mesa = $mesaModel->getMesaById(intval($idMesa));
            
            if (!$mesa || !$mesa['activa']) {
                http_response_code(400);
                echo json_encode(['status'=>'error','message'=>'mesa_not_active', 'detail'=>'La mesa no está activa']);
                return;
            }
            
            if ($mesa['estado'] === 'Ocupada') {
                http_response_code(400);
                echo json_encode(['status'=>'error','message'=>'mesa_occupied', 'detail'=>'La mesa ya está Ocupada']);
                return;
            }
            
            $ok = $res->confirm(intval($id), intval($idMesa));
        } else {
            $ok = $res->confirm(intval($id));
        }
        
        echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo confirmar']);
    }

     /*
     * cancelar()
     * Elimina (cancela) una reserva.
     * Si la reserva tenía mesa asignada, la libera automáticamente.
     * 
     * Validaciones:
     * - La reserva debe existir
     * - Solo se pueden cancelar reservas 'pendiente' o 'confirmada'
     * 
     * @return void - Retorna JSON
     */
    public function cancelar() {
        header('Content-Type: application/json; charset=utf-8');
        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        
        if (!$id || !ctype_digit($id)) { 
            http_response_code(400); 
            echo json_encode(['status'=>'error','message'=>'missing_id']); 
            return; 
        }
        
        $res = new ReservaModel();
        
        // Verificar que la reserva existe
        $reserva = $res->getById(intval($id));
        if (!$reserva) {
            http_response_code(404);
            echo json_encode(['status'=>'error','message'=>'reservation_not_found']);
            return;
        }
        
        // No permitir cancelar reservas ya canceladas
        if ($reserva['estado'] === 'cancelada') {
            http_response_code(400);
            echo json_encode(['status'=>'error','message'=>'already_cancelled', 'detail'=>'La reserva ya fue cancelada']);
            return;
        }
        
        $ok = $res->delete(intval($id));
        echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo eliminar reserva']);
    }

     /*
     * obtenerMesasDisponiblesPorFecha()
     * 
     * Endpoint para obtener las mesas disponibles para una fecha específica.
     * Implementa la nueva lógica de disponibilidad dinámica.
     * 
     * @return void - Retorna JSON con array de mesas disponibles
     */
    public function obtenerMesasDisponiblesPorFecha() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $fecha = $_GET['fecha'] ?? null;
            
            if (!$fecha) {
                http_response_code(400);
                echo json_encode(['status'=>'error','message'=>'missing_fecha']);
                return;
            }
            
            error_log("Obteniendo mesas disponibles para fecha: $fecha");
            
            $res = new ReservaModel();
            $mesasDisponibles = $res->getMesasActivasYDisponibles($fecha);
            
            error_log("Mesas disponibles encontradas: " . count($mesasDisponibles));
            
            echo json_encode($mesasDisponibles);
        } catch (Exception $e) {
            error_log("Error en obtenerMesasDisponiblesPorFecha: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status'=>'error','message'=>'exception', 'detail'=>$e->getMessage()]);
        }
    }

     /*
     * obtenerReservasPorFecha()
     * 
     * Endpoint para obtener todas las mesas con sus reservas para una fecha específica.
     * 
     * @return void - Retorna JSON
     */
    public function obtenerReservasPorFecha() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $fecha = $_GET['fecha'] ?? null;
            
            if (!$fecha) {
                http_response_code(400);
                echo json_encode(['status'=>'error','message'=>'missing_fecha']);
                return;
            }
            
            error_log("Obteniendo reservas para fecha: $fecha");
            
            $res = new ReservaModel();
            $resultado = $res->getReservasPorFechaConMesas($fecha);
            
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en obtenerReservasPorFecha: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status'=>'error','message'=>'exception', 'detail'=>$e->getMessage()]);
        }
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
