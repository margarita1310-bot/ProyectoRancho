<?php
 /*
 * DisponibilidadController.php
 * 
 * Controlador para gestión de disponibilidad de mesas por fecha.
 * Permite crear, consultar, actualizar y eliminar registros de disponibilidad.
 * 
 * Acciones:
 * - listar: GET - Obtiene disponibilidad para una fecha específica (?fecha=YYYY-MM-DD)
 * - guardar: POST - Crea disponibilidad para fecha (UPSERT: actualiza si existe)
 * - actualizar: POST - Actualiza cantidad de mesas
 * - eliminar: POST - Elimina registro de disponibilidad
 * 
 * Tabla de base de datos: mesas_disponibilidad (id, fecha, cantidad, created_at)
 * 
 * Requiere: Autenticación de administrador (ensureAdmin())
 */

require_once __DIR__ . '/../models/DisponibilidadModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

class DisponibilidadController {
     /*
     * listar()
     * 
     * Retorna disponibilidad de mesas para una fecha específica.
     * @return void - Retorna JSON
     */
    public function listar() {
        header('Content-Type: application/json; charset=utf-8');
        $fecha = $_GET['fecha'] ?? null;
        if (!$fecha) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_fecha']); return; }
        $m = new DisponibilidadModel();
        $res = $m->getByDate($fecha);
        echo json_encode($res ?: ['fecha'=>$fecha,'cantidad'=>0]);
    }

     /*
     * guardar()
     * Crea disponibilidad para una fecha o actualiza si ya existe (UPSERT).
     * Valida que no existan reservas activas antes de permitir la creación.
     * Al guardar, activa automáticamente las mesas correspondientes.
     * @return void - Retorna JSON
     */
    public function guardar() {
        header('Content-Type: application/json; charset=utf-8');
        $fecha = $_POST['fecha'] ?? null;
        $cantidad = $_POST['cantidad'] ?? null;
        
        if (!$fecha || !$cantidad || !ctype_digit(strval($cantidad))) { 
            http_response_code(400); 
            echo json_encode(['status'=>'error','message'=>'invalid_input']); 
            return; 
        }
        
        $m = new DisponibilidadModel();
        
        // Verificar si hay reservas activas para esa fecha
        if ($m->tieneReservas($fecha)) {
            http_response_code(409);
            echo json_encode(['status'=>'error','message'=>'has_reservations', 'detail'=>'No se puede modificar la disponibilidad porque existen reservas activas para esta fecha']);
            return;
        }
        
        $ok = $m->create($fecha, intval($cantidad));
        echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'db_error']);
    }

     /*
     * actualizar()
     * 
     * Actualiza la cantidad de mesas disponibles para un registro.
     * Valida que no existan reservas activas antes de permitir la modificación.
     * 
     * Parámetros esperados (POST):
     * - id: ID del registro (numeric, obligatoria)
     * - cantidad: nueva cantidad (numeric, obligatoria)
     * - fecha: fecha del registro (para validar reservas)
     * 
     * Respuestas:
     * - 200: {"status":"ok"}
     * - 400: {"status":"error","message":"invalid_input"}
     * - 409: {"status":"error","message":"has_reservations"}
     * - 500: {"status":"error","message":"db_error"}
     * 
     * @return void - Retorna JSON
     */
    public function actualizar() {
        header('Content-Type: application/json; charset=utf-8');
        $id = $_POST['id'] ?? null;
        $cantidad = $_POST['cantidad'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        
        if (!$id || !ctype_digit(strval($id)) || !$cantidad || !ctype_digit(strval($cantidad))) { 
            http_response_code(400); 
            echo json_encode(['status'=>'error','message'=>'invalid_input']); 
            return; 
        }
        
        $m = new DisponibilidadModel();
        
        // Si se proporciona fecha, verificar reservas
        if ($fecha && $m->tieneReservas($fecha)) {
            http_response_code(409);
            echo json_encode(['status'=>'error','message'=>'has_reservations', 'detail'=>'No se puede modificar la disponibilidad porque existen reservas activas']);
            return;
        }
        
        $ok = $m->update(intval($id), intval($cantidad));
        echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'db_error']);
    }

     /*
     * eliminar()
     * 
     * Elimina un registro de disponibilidad de mesas.
     * 
     * Parámetros esperados (POST):
     * - id: ID del registro (numeric, obligatoria)
     * 
     * Respuestas:
     * - 200: {"status":"ok"}
     * - 400: {"status":"error","message":"invalid_input"}
     * - 500: {"status":"error","message":"db_error"}
     * 
     * @return void - Retorna JSON
     */
    public function eliminar() {
        header('Content-Type: application/json; charset=utf-8');
        $id = $_POST['id'] ?? null;
        if (!$id || !ctype_digit(strval($id))) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'invalid_input']); return; }
        $m = new DisponibilidadModel();
        $ok = $m->delete(intval($id));
        echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'db_error']);
    }
}

$controller = new DisponibilidadController();
$action = $_GET['action'] ?? null;
if (!$action) {
    $isAjax = false;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') $isAjax = true;
    if (!$isAjax && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) $isAjax = true;
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'action_not_found']);
        exit;
    }
    echo 'Acción no encontrada.'; exit;
}
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
