<?php

require_once __DIR__ . '/../models/PromocionModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

/**
 * Controlador de Promociones
 * Gestiona la creación, actualización, obtención y eliminación de promociones
 * Incluye manejo de imágenes y productos asociados a cada promoción
 */
class PromocionController
{
    /**
     * Lista todas las promociones con sus productos asociados.
     * Si es solicitud AJAX, retorna promociones en JSON.
     * Si no, renderiza el dashboard administrativo.
     * 
     * @return void Envía respuesta JSON o renderiza vista
     */
    public function index()
    {
        $prom = new PromocionModel();
        $promocion = $prom->getPromocionesConProductos();

        // Verificar si es solicitud AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($promocion);
            return;
        }

        // Renderizar dashboard si no es AJAX
        require_once __DIR__ . '/../views/admin/DashboardAdmin.php';
    }

    /**
     * Crea una nueva promoción con validación de datos.
     * Procesa imagen de promoción si se proporciona y asocia productos.
     * 
     * @return void Envía respuesta JSON con ID de la promoción o errores
     */
    public function guardar()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Obtener datos del formulario
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
        $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
        $fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : null;
        $estado = isset($_POST['estado']) ? trim($_POST['estado']) : null;
        $productos = isset($_POST['productos']) ? $_POST['productos'] : [];

        // Validar datos
        $errors = [];
        if (!$nombre) {
            $errors[] = 'nombre_required';
        }
        if (!$descripcion) {
            $errors[] = 'descripcion_required';
        }
        if ($fecha_inicio) {
            $d = DateTime::createFromFormat('Y-m-d', $fecha_inicio);
            if (!$d) {
                $errors[] = 'fecha_inicio_invalid';
            }
        }
        if ($fecha_fin) {
            $d2 = DateTime::createFromFormat('Y-m-d', $fecha_fin);
            if (!$d2) {
                $errors[] = 'fecha_fin_invalid';
            }
        }

        $allowedStates = ['Disponible', 'No disponible'];
        if ($estado && !in_array($estado, $allowedStates)) {
            $errors[] = 'estado_invalid';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // Crear promoción en base de datos
        $prom = new PromocionModel();
        $newId = $prom->create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado);

        if ($newId === false) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'no se pudo crear promocion']);
            return;
        }

        // Asociar productos a la promoción
        if (!empty($productos) && is_array($productos)) {
            $prom->setProductosToPromocion($newId, $productos);
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
            $uploadDir = __DIR__ . '/../../public/images/promocion/';

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
        echo json_encode(['status' => 'ok', 'id_promocion' => $newId]);
    }

    /**
     * Obtiene los datos de una promoción específica por su ID.
     * Incluye productos asociados a la promoción.
     * 
     * @return void Envía respuesta JSON con datos de la promoción o error
     */
    public function obtener()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $id = $_POST['id'] ?? null;

            // Validar ID
            if (!$id || !ctype_digit($id)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'missing_id']);
                return;
            }

            // Obtener promoción
            $prom = new PromocionModel();
            $data = $prom->getById(intval($id));

            if (!$data) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'promocion_not_found']);
                return;
            }

            // Obtener productos asociados
            $data['productos'] = $prom->getProductosByPromocionId(intval($id));
            echo json_encode($data);
        } catch (Exception $e) {
            error_log("Error en PromocionController::obtener - " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'internal_error', 'details' => $e->getMessage()]);
        }
    }

    /**
     * Actualiza los datos de una promoción existente.
     * Permite modificar información, reemplazar imagen y cambiar productos asociados.
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
        $fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
        $fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : null;
        $estado = isset($_POST['estado']) ? trim($_POST['estado']) : null;
        $productos = isset($_POST['productos']) ? $_POST['productos'] : [];

        // Validar datos
        $errors = [];
        if (!$id || !ctype_digit($id)) {
            $errors[] = 'id_invalid';
        }
        if (!$nombre) {
            $errors[] = 'nombre_required';
        }
        if ($fecha_inicio) {
            $d = DateTime::createFromFormat('Y-m-d', $fecha_inicio);
            if (!$d) {
                $errors[] = 'fecha_inicio_invalid';
            }
        }
        if ($fecha_fin) {
            $d2 = DateTime::createFromFormat('Y-m-d', $fecha_fin);
            if (!$d2) {
                $errors[] = 'fecha_fin_invalid';
            }
        }

        $allowedStates = ['Disponible', 'No disponible'];
        if ($estado && !in_array($estado, $allowedStates)) {
            $errors[] = 'estado_invalid';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // Actualizar promoción
        $prom = new PromocionModel();
        $ok = $prom->update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado);

        // Actualizar productos asociados
        if ($ok && is_array($productos)) {
            $prom->setProductosToPromocion($id, $productos);
        }

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
            $uploadDir = __DIR__ . '/../../public/images/promocion/';

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
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo actualizar promocion']);
    }

    /**
     * Elimina una promoción por su ID.
     * También elimina la imagen asociada.
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

        // Eliminar promoción
        $prom = new PromocionModel();
        $ok = $prom->delete(intval($id));

        if ($ok) {
            // Eliminar imágenes asociadas
            $dir = __DIR__ . '/../../public/images/promocion/';
            foreach (['jpg', 'png'] as $e) {
                $p = $dir . intval($id) . '.' . $e;
                if (is_file($p)) {
                    @unlink($p);
                }
            }
        }

        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo eliminar promocion']);
    }

    /**
     * Obtiene la lista de todos los productos disponibles.
     * Utilizado para asociar productos a una promoción.
     * 
     * @return void Envía respuesta JSON con lista de productos
     */
    public function getProductos()
    {
        header('Content-Type: application/json; charset=utf-8');

        require_once __DIR__ . '/../models/ProductoModel.php';

        $productoModel = new ProductoModel();
        $productos = $productoModel->getAll();
        echo json_encode($productos);
    }
}

// Instanciar el controlador y ejecutar la acción solicitada
$controller = new PromocionController();
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