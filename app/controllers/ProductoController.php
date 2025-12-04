<?php

require_once __DIR__ . '/../models/ProductoModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

/**
 * Controlador de Productos
 * Gestiona la creación, actualización, obtención y eliminación de productos
 */
class ProductoController
{
    /**
     * Lista todos los productos del sistema.
     * Si es solicitud AJAX, retorna productos en JSON.
     * Si no, renderiza el dashboard administrativo.
     * 
     * @return void Envía respuesta JSON o renderiza vista
     */
    public function index()
    {
        $productoModel = new ProductoModel();
        $producto = $productoModel->getAll();

        // Verificar si es solicitud AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($producto);
            return;
        }

        // Renderizar dashboard si no es AJAX
        require_once __DIR__ . '/../views/admin/DashboardAdmin.php';
    }

    /**
     * Crea un nuevo producto con validación de datos.
     * Valida nombre, precio (numérico y positivo) y categoría.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function guardar()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Obtener datos del formulario
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $precio = isset($_POST['precio']) ? trim($_POST['precio']) : null;
        $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : null;

        // Validar datos
        $errors = [];
        if (!$nombre) {
            $errors[] = 'nombre_required';
        }
        if (!$precio || !is_numeric($precio) || floatval($precio) < 0) {
            $errors[] = 'precio_invalid';
        }
        if (!$categoria) {
            $errors[] = 'categoria_required';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // Crear producto en base de datos
        $producto = new ProductoModel();
        $ok = $producto->create($nombre, floatval($precio), $categoria);

        header('Content-Type: application/json; charset=utf-8');
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo crear el producto']);
    }

    /**
     * Obtiene los datos de un producto específico por su ID.
     * Valida que el ID sea numérico y positivo.
     * 
     * @return void Envía respuesta JSON con datos del producto o error
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

        // Obtener y retornar producto
        $producto = new ProductoModel();
        echo json_encode($producto->getById(intval($id)));
    }

    /**
     * Actualiza los datos de un producto existente.
     * Valida nombre, precio (numérico y positivo), categoría e ID del producto.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function actualizar()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Obtener datos del formulario
        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $precio = isset($_POST['precio']) ? trim($_POST['precio']) : null;
        $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : null;

        // Validar datos
        $errors = [];
        if (!$id || !ctype_digit($id)) {
            $errors[] = 'id_invalid';
        }
        if (!$nombre) {
            $errors[] = 'nombre_required';
        }
        if (!$precio || !is_numeric($precio) || floatval($precio) < 0) {
            $errors[] = 'precio_invalid';
        }
        if (!$categoria) {
            $errors[] = 'categoria_required';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            return;
        }

        // Actualizar producto en base de datos
        $producto = new ProductoModel();
        $ok = $producto->update(intval($id), $nombre, floatval($precio), $categoria);

        header('Content-Type: application/json; charset=utf-8');
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo actualizar el producto']);
    }

    /**
     * Elimina un producto por su ID.
     * Valida que el ID sea numérico y positivo.
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

        // Eliminar producto
        $producto = new ProductoModel();
        $ok = $producto->delete(intval($id));
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo eliminar el producto']);
    }
}

// Instanciar el controlador y ejecutar la acción solicitada
$controller = new ProductoController();
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
        echo "Acción no válida.";
    }
}
?>