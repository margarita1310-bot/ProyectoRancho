<?php

require_once __DIR__ . '/../models/EventoModel.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/../helpers/Functions.php';

ensureAdmin();

/**
 * Controlador de Eventos
 * Gestiona la creación, actualización, obtención y eliminación de eventos
 * Incluye manejo de imágenes asociadas a cada evento
 */
class EventoController
{
    /**
     * Lista todos los eventos del sistema.
     * Si es solicitud AJAX, retorna eventos en JSON con información de imágenes.
     * Si no, renderiza el dashboard administrativo.
     * 
     * @return void Envía respuesta JSON o renderiza vista
     */
    public function index()
    {
        $ev = new EventoModel();
        $evento = $ev->getAll();

        // Verificar si es solicitud AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json; charset=utf-8');

            $uploadDir = __DIR__ . '/../../public/images/evento/';

            // Agregar información de imágenes a cada evento
            foreach ($evento as &$ev) {
                $id = $ev['id_evento'];
                $ev['imagen'] = null;

                foreach (['jpg', 'png'] as $ext) {
                    $archivo = $id . '.' . $ext;
                    if (file_exists($uploadDir . $archivo)) {
                        $ev['imagen'] = $archivo;
                        break;
                    }
                }
            }

            echo json_encode($evento);
            return;
        }

        // Renderizar dashboard si no es AJAX
        require_once __DIR__ . '/../views/admin/DashboardAdmin.php';
    }

    /**
     * Crea un nuevo evento con validación de datos.
     * Procesa imagen de evento si se proporciona y valida formato y tamaño.
     * 
     * @return void Envía respuesta JSON con ID del evento o errores
     */
    public function guardar()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Obtener datos del formulario
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : null;
        $hora_inicio = isset($_POST['hora_inicio']) ? trim($_POST['hora_inicio']) : null;
        $hora_fin = isset($_POST['hora_fin']) ? trim($_POST['hora_fin']) : null;

        // Validar datos
        $errors = [];
        if (!$nombre) {
            $errors[] = 'nombre_required';
        }
        if (!$fecha || !DateTime::createFromFormat('Y-m-d', $fecha)) {
            $errors[] = 'fecha_invalid';
        }
        if (!$hora_inicio || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora_inicio)) {
            $errors[] = 'hora_inicio_invalid';
        }
        if ($hora_fin && !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora_fin)) {
            $errors[] = 'hora_fin_invalid';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // Normalizar horas
        $hora_inicio = normalizarHora($hora_inicio);
        $hora_fin = normalizarHora($hora_fin);

        // Crear evento en base de datos
        $ev = new EventoModel();
        $newId = $ev->create($nombre, $descripcion, $fecha, $hora_inicio, $hora_fin);

        if ($newId === false) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'no se pudo crear evento']);
            return;
        }

        // Procesar imagen si se proporcionó
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $maxSize = 2 * 1024 * 1024;
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

            // Validar tamaño
            if ($_FILES['imagen']['size'] > $maxSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'errors' => ['imagen_too_large']]);
                return;
            }

            // Validar tipo MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
            finfo_close($finfo);

            if (!isset($allowed[$mime])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'errors' => ['imagen_invalid_type']]);
                return;
            }

            $ext = $allowed[$mime];
            $uploadDir = __DIR__ . '/../../public/images/evento/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Eliminar imágenes anteriores
            foreach (['jpg', 'png'] as $e) {
                $p = $uploadDir . $newId . '.' . $e;
                if (is_file($p)) {
                    @unlink($p);
                }
            }

            // Guardar nueva imagen
            $dest = $uploadDir . $newId . '.' . $ext;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'upload_failed']);
                return;
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'ok', 'id_evento' => $newId]);
    }

    /**
     * Obtiene los datos de un evento específico por su ID.
     * Incluye información sobre la existencia de imagen asociada.
     * 
     * @return void Envía respuesta JSON con datos del evento o error
     */
    public function obtener()
    {
        $id = $_POST['id'] ?? null;

        // Validar ID
        if (!$id || !ctype_digit($id)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'missing_id']);
            return;
        }

        header('Content-Type: application/json; charset=utf-8');

        // Obtener evento
        $ev = new EventoModel();
        $evento = $ev->getById(intval($id));

        if ($evento) {
            // Buscar imagen asociada
            $uploadDir = __DIR__ . '/../../public/images/evento/';
            $evento['imagen'] = null;

            foreach (['jpg', 'png'] as $ext) {
                $archivo = $id . '.' . $ext;
                if (file_exists($uploadDir . $archivo)) {
                    $evento['imagen'] = $archivo;
                    $evento['tiene_imagen'] = true;
                    break;
                }
            }

            if (!$evento['imagen']) {
                $evento['tiene_imagen'] = false;
            }
        }

        echo json_encode($evento);
    }

    /**
     * Actualiza los datos de un evento existente.
     * Permite modificar información y reemplazar imagen si se proporciona nueva.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function actualizar()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Obtener datos del formulario
        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : null;
        $hora_inicio = isset($_POST['hora_inicio']) ? trim($_POST['hora_inicio']) : null;
        $hora_fin = isset($_POST['hora_fin']) ? trim($_POST['hora_fin']) : null;

        // Validar datos
        $errors = [];
        if (!$id || !ctype_digit($id)) {
            $errors[] = 'id_invalid';
        }
        if (!$nombre) {
            $errors[] = 'nombre_required';
        }
        if (!$fecha || !DateTime::createFromFormat('Y-m-d', $fecha)) {
            $errors[] = 'fecha_invalid';
        }
        if (!$hora_inicio || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora_inicio)) {
            $errors[] = 'hora_inicio_invalid';
        }
        if ($hora_fin && !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora_fin)) {
            $errors[] = 'hora_fin_invalid';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // Normalizar horas
        $hora_inicio = normalizarHora($hora_inicio);
        $hora_fin = normalizarHora($hora_fin);

        // Actualizar evento
        $ev = new EventoModel();
        $ok = $ev->update($id, $nombre, $descripcion, $fecha, $hora_inicio, $hora_fin);

        // Procesar imagen si se proporcionó
        if ($ok && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $maxSize = 2 * 1024 * 1024;
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

            // Validar tamaño
            if ($_FILES['imagen']['size'] > $maxSize) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'errors' => ['imagen_too_large']]);
                return;
            }

            // Validar tipo MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
            finfo_close($finfo);

            if (!isset($allowed[$mime])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'errors' => ['imagen_invalid_type']]);
                return;
            }

            $ext = $allowed[$mime];
            $uploadDir = __DIR__ . '/../../public/images/evento/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Eliminar imágenes anteriores
            foreach (['jpg', 'png'] as $e) {
                $p = $uploadDir . $id . '.' . $e;
                if (is_file($p)) {
                    @unlink($p);
                }
            }

            // Guardar nueva imagen
            $dest = $uploadDir . $id . '.' . $ext;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'upload_failed']);
                return;
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo actualizar evento']);
    }

    /**
     * Elimina un evento por su ID.
     * También elimina la imagen asociada y maneja errores de integridad referencial.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function eliminar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = $_POST['id'] ?? null;

        // Validar ID
        if (!$id || !ctype_digit($id)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'missing_id']);
            return;
        }

        $ev = new EventoModel();

        try {
            // Eliminar evento de base de datos
            $ok = $ev->delete(intval($id));

            if ($ok) {
                // Eliminar imágenes asociadas
                $dir = __DIR__ . '/../../public/images/evento/';
                foreach (['jpg', 'png'] as $e) {
                    $p = $dir . intval($id) . '.' . $e;
                    if (is_file($p)) {
                        @unlink($p);
                    }
                }
            }

            echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo eliminar evento']);
        } catch (PDOException $e) {
            // Manejar error de integridad referencial (evento con reservas)
            if ($e->getCode() == '23000') {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se puede eliminar el evento porque tiene reservas asociadas. Elimina primero las reservas.'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar: ' . $e->getMessage()]);
            }
        }
    }
}

// Instanciar el controlador y ejecutar la acción solicitada
$controller = new EventoController();
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