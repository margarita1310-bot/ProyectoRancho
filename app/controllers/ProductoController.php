<?php
 /*
 * ProductoController.php
 * 
 * Controlador CRUD de Productos (menú del restaurante).
 * Gestiona la creación, lectura, actualización y eliminación de productos.
 * 
 * Acciones:
 * - index: GET - Lista todos los productos
 * - guardar: POST - Crea nuevo producto (nombre, precio, categoria, imagen)
 * - obtener: POST - Obtiene datos de un producto específico (JSON)
 * - actualizar: POST - Actualiza un producto existente (JSON)
 * - eliminar: POST - Elimina un producto (JSON)
 * 
 * Tabla de base de datos: producto (id_producto, nombre, precio, categoria)
 * 
 * Requiere: Autenticación de administrador (ensureAdmin())
 */

require_once __DIR__ . '/../models/ProductoModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

class ProductoController {
     /*
     * index()
     * Muestra la lista completa de productos.
     * @return void - Incluye vista menu.php
     */
    public function index() {
        $producto = new ProductoModel();
        $producto = $producto->getAll();
        require_once __DIR__ . '/../../app/views/admin/ProductoAdmin.php';
    }

     /*
     * guardar()
     * Crea un nuevo producto en la base de datos.
     * @return void - Retorna JSON
     */
    public function guardar() {
        header('Content-Type: application/json; charset=utf-8');
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $precio = isset($_POST['precio']) ? trim($_POST['precio']) : null;
        $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : null;

        // Validar parámetros de texto
        $errors = [];
        if (!$nombre) $errors[] = 'nombre_required';
        if (!$precio || !is_numeric($precio) || floatval($precio) < 0) $errors[] = 'precio_invalid';
        if (!$categoria) $errors[] = 'categoria_required';

        if (!empty($errors)) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>$errors]); return; }

        // Crear producto
        $producto = new ProductoModel();
        $ok = $producto->create($nombre, floatval($precio), $categoria);
		header('Content-Type: application/json; charset=utf-8');
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo crear el producto']);
    }

     /*
     * obtener()
     * Obtiene los datos de un producto específico por ID.
     * @return void - Retorna JSON con datos del producto
     */
    public function obtener() {
        $id = $_POST['id'] ?? null;
        if (!$id || !ctype_digit($id)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_id']); return; }
        header('Content-Type: application/json; charset=utf-8');
        $producto = new ProductoModel();
        echo json_encode($producto->getById(intval($id)));
    }

     /*
     * actualizar()
     * Actualiza un producto existente en la base de datos.
     * @return void - Retorna JSON
     */
    public function actualizar() {
        header('Content-Type: application/json; charset=utf-8');
        $id = isset($_POST['id']) ? trim($_POST['id']) : null;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $precio = isset($_POST['precio']) ? trim($_POST['precio']) : null;
        $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : null;

        // Validar parámetros
        $errors = [];
        if (!$id || !ctype_digit($id)) $errors[] = 'id_invalid';
        if (!$nombre) $errors[] = 'nombre_required';
        if (!$precio || !is_numeric($precio) || floatval($precio) < 0) $errors[] = 'precio_invalid';
        if (!$categoria) $errors[] = 'categoria_required';

        if (!empty($errors)) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>$errors]); return; }

        // Actualizar producto
        $producto = new ProductoModel();
        $ok = $producto->update(intval($id), $nombre, floatval($precio), $categoria);
		header('Content-Type: application/json; charset=utf-8');
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo actualizar el producto']);
    }

     /*
     * eliminar()
     * Elimina un producto de la base de datos.
     * @return void - Retorna JSON
     */
    public function eliminar() {
        header('Content-Type: application/json; charset=utf-8');
        $id = $_POST['id'] ?? null;
        if (!$id || !ctype_digit($id)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_id']); return; }
        $producto = new ProductoModel();
        $ok = $producto->delete(intval($id));
        echo $ok ? json_encode(['status' => 'ok']) : json_encode(['status' => 'error', 'message' => 'no se pudo eliminar el producto']);
    }
}

$controller = new ProductoController();
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
        echo "Acción no válida.";
    }
}
?>