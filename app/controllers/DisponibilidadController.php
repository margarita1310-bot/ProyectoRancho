<?php

require_once __DIR__ . '/../models/DisponibilidadModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

/**
 * Controlador de Disponibilidad
 * Gestiona las disponibilidades de fechas para reservas y eventos
 */
class DisponibilidadController
{
    /**
     * Lista la disponibilidad para una fecha específica.
     * Retorna información de cantidad de espacios disponibles.
     * 
     * @return void Envía respuesta JSON con disponibilidad o error
     */
    public function listar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $fecha = $_GET['fecha'] ?? null;

        // Validar que la fecha sea proporcionada
        if (!$fecha) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'missing_fecha']);
            return;
        }

        // Obtener disponibilidad por fecha
        $m = new DisponibilidadModel();
        $res = $m->getByDate($fecha);
        echo json_encode($res ?: ['fecha' => $fecha, 'cantidad' => 0]);
    }

    /**
     * Guarda una nueva disponibilidad para una fecha.
     * Valida que no existan reservas activas antes de guardar.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function guardar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $fecha = $_POST['fecha'] ?? null;
        $cantidad = $_POST['cantidad'] ?? null;

        error_log("Datos recibidos - Fecha: $fecha, Cantidad: $cantidad");

        // Validar entrada
        if (!$fecha || !$cantidad || !ctype_digit(strval($cantidad))) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'invalid_input',
                'detail' => 'Fecha o cantidad inválida'
            ]);
            return;
        }

        $m = new DisponibilidadModel();

        // Verificar si la fecha tiene reservas activas
        if ($m->tieneReservas($fecha)) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => 'has_reservations',
                'detail' => 'No se puede modificar la disponibilidad porque existen reservas activas para esta fecha'
            ]);
            return;
        }

        try {
            // Guardar la disponibilidad
            $ok = $m->create($fecha, intval($cantidad));
            if ($ok) {
                echo json_encode(['status' => 'ok', 'message' => 'Disponibilidad guardada correctamente']);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'db_error',
                    'detail' => 'No se pudo guardar en la base de datos'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error al guardar disponibilidad: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'exception',
                'detail' => $e->getMessage()
            ]);
        }
    }

    /**
     * Actualiza la cantidad disponible para una fecha específica.
     * Verifica que no existan reservas activas antes de actualizar.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function actualizar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = $_POST['id'] ?? null;
        $cantidad = $_POST['cantidad'] ?? null;
        $fecha = $_POST['fecha'] ?? null;

        // Validar entrada
        if (!$id || !ctype_digit(strval($id)) || !$cantidad || !ctype_digit(strval($cantidad))) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'invalid_input']);
            return;
        }

        $m = new DisponibilidadModel();

        // Verificar si la fecha tiene reservas activas
        if ($fecha && $m->tieneReservas($fecha)) {
            http_response_code(409);
            echo json_encode([
                'status' => 'error',
                'message' => 'has_reservations',
                'detail' => 'No se puede modificar la disponibilidad porque existen reservas activas'
            ]);
            return;
        }

        // Actualizar disponibilidad
        $ok = $m->update(intval($id), intval($cantidad));
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'db_error']);
    }

    /**
     * Elimina una disponibilidad por su ID.
     * Cancela los espacios disponibles para la fecha asociada.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function eliminar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = $_POST['id'] ?? null;

        // Validar que el ID sea válido
        if (!$id || !ctype_digit(strval($id))) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'invalid_input']);
            return;
        }

        // Eliminar disponibilidad
        $m = new DisponibilidadModel();
        $ok = $m->delete(intval($id));
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'db_error']);
    }

    /**
     * Lista todas las disponibilidades con información sobre reservas activas.
     * Retorna un array con todos los registros de disponibilidad.
     * 
     * @return void Envía respuesta JSON con todas las disponibilidades
     */
    public function listarTodas()
    {
        header('Content-Type: application/json; charset=utf-8');

        $m = new DisponibilidadModel();
        $disponibilidades = $m->getAllWithReservationCheck();
        echo json_encode($disponibilidades);
    }
}

// Instanciar el controlador y ejecutar la acción solicitada
$controller = new DisponibilidadController();
$action = $_GET['action'] ?? null;

if (!$action) {
    // Verificar si es una solicitud AJAX
    $isAjax = false;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $isAjax = true;
    }
    if (!$isAjax && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        $isAjax = true;
    }

    // Retornar error apropiado
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'action_not_found']);
    } else {
        echo 'Acción no encontrada.';
    }
    exit;
}

// Ejecutar acción si existe
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