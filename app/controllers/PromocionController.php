<?php
 /*
 * PromocionController.php
 * 
 * Controlador CRUD de Promociones con gestión de imágenes.
 * Crea, lee, actualiza y elimina promociones. Las imágenes se guardan en public/images/promocion/
 * 
 * Acciones:
 * - index: GET - Lista todas las promociones
 * - guardar: POST - Crea nueva promoción (multipart/form-data con imagen opcional)
 * - obtener: POST - Obtiene datos de una promoción (JSON)
 * - actualizar: POST - Actualiza una promoción (multipart/form-data con imagen opcional)
 * - eliminar: POST - Elimina una promoción y su imagen (JSON)
 * 
 * Tabla de base de datos: promocion (id_promocion, nombre, descripcion, fecha_inicio, fecha_fin, estado)
 * 
 * Requiere: Autenticación de administrador (ensureAdmin())
 * 
 * Validación de imágenes:
 * - Opcional en crear y actualizar
 * - Máximo 2MB
 * - Tipos permitidos: JPEG, PNG (validados por MIME type)
 * - Nombre por ID: {id_promocion}.{ext}
 * - Directorio: public/images/promocion/
 */

require_once __DIR__ . '/../models/PromocionModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

class PromocionController {
	 /*
	 * index()
	 * Muestra la lista completa de promociones.
	 * @return void - Incluye vista PromocionView.php o retorna JSON si es AJAX
	 */
	public function index() {
		$prom = new PromocionModel();
		$promocion = $prom->getAll();
		
		// Si es petición AJAX, devolver JSON
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($promocion);
			return;
		}
		
		// Si no es AJAX, mostrar vista completa
		require_once __DIR__ . '/../views/admin/DashboardAdmin.php';
	}

	 /*
	 * guardar()
	 * Crea una nueva promoción en la base de datos con imagen opcional.
	 * @return void - Retorna JSON
	 */
	public function guardar() {
		header('Content-Type: application/json; charset=utf-8');
		$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
		$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
		$fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
		$fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : null;
		$estado = isset($_POST['estado']) ? trim($_POST['estado']) : null;

		// Validar parámetros de texto
		$errors = [];
		if (!$nombre) $errors[] = 'nombre_required';
		if (!$descripcion) $errors[] = 'descripcion_required';
		if ($fecha_inicio) { $d = DateTime::createFromFormat('Y-m-d', $fecha_inicio); if (!$d) $errors[] = 'fecha_inicio_invalid';}
		if ($fecha_fin) { $d2 = DateTime::createFromFormat('Y-m-d', $fecha_fin); if (!$d2) $errors[] = 'fecha_fin_invalid'; }
		$allowedStates = ['Disponible','No disponible'];
		if ($estado && !in_array($estado, $allowedStates)) $errors[] = 'estado_invalid';
		
		if (!empty($errors)) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>$errors]); return; }

		// Crear promoción (sin imagen en BD)
		$prom = new PromocionModel();
		$newId = $prom->create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado);
		if ($newId === false) { http_response_code(500); echo json_encode(['status'=>'error','message'=>'no se pudo crear promocion']); return; }

		// Procesar imagen si se subió (opcional) y guardarla como {id}.{ext}
		if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
			$maxSize = 2 * 1024 * 1024; // 2MB
			$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
			if ($_FILES['imagen']['size'] > $maxSize) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>['imagen_too_large']]); return; }
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
			finfo_close($finfo);
			if (!isset($allowed[$mime])) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>['imagen_invalid_type']]); return; }
			$ext = $allowed[$mime];
			$uploadDir = __DIR__ . '/../../public/images/promocion/';
			if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
			// Eliminar por seguridad si existiera
			foreach (['jpg','png'] as $e) { $p = $uploadDir . $newId . '.' . $e; if (is_file($p)) @unlink($p); }
			$dest = $uploadDir . $newId . '.' . $ext;
			if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) { http_response_code(500); echo json_encode(['status'=>'error','message'=>'upload_failed']); return; }
		}

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['status'=>'ok', 'id_promocion' => $newId]);
	}

	 /*
	 * obtener()
	 * Obtiene los datos de una promoción por ID.
	 * @return void - Retorna JSON con datos de la promoción
	 */
	public function obtener() {
		$id = $_POST['id'] ?? null;
		if (!$id || !ctype_digit($id)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_id']); return; }
		header('Content-Type: application/json; charset=utf-8'); 
		$prom = new PromocionModel();
		echo json_encode($prom->getById(intval($id)));
	}

	 /*
	 * actualizar()
	 * Actualiza promoción con nueva imagen opcional.
	 * @return void - Retorna JSON
	 */
	public function actualizar() {
		header('Content-Type: application/json; charset=utf-8');
		$id = isset($_POST['id']) ? trim($_POST['id']) : null;
		$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
		$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
		$fecha_inicio = isset($_POST['fecha_inicio']) ? trim($_POST['fecha_inicio']) : null;
		$fecha_fin = isset($_POST['fecha_fin']) ? trim($_POST['fecha_fin']) : null;
		$estado = isset($_POST['estado']) ? trim($_POST['estado']) : null;

		// Validar parámetros
		$errors = [];
		if (!$id || !ctype_digit($id)) $errors[] = 'id_invalid';
		if (!$nombre) $errors[] = 'nombre_required';
		if ($fecha_inicio) { $d = DateTime::createFromFormat('Y-m-d', $fecha_inicio); if (!$d) $errors[] = 'fecha_inicio_invalid'; }
		if ($fecha_fin) { $d2 = DateTime::createFromFormat('Y-m-d', $fecha_fin); if (!$d2) $errors[] = 'fecha_fin_invalid'; }
		$allowedStates = ['Disponible','No disponible']; 
		if ($estado && !in_array($estado, $allowedStates)) $errors[] = 'estado_invalid';

		if (!empty($errors)) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>$errors]); return; }

		// Actualizar promoción (sin imagen en BD)
		$prom = new PromocionModel();
		$ok = $prom->update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado);

		// Si se subió nueva imagen, guardarla como {id}.{ext} y eliminar otras extensiones
		if ($ok && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
			$maxSize = 2 * 1024 * 1024;
			$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
			if ($_FILES['imagen']['size'] > $maxSize) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>['imagen_too_large']]); return; }
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
			finfo_close($finfo);
			if (!isset($allowed[$mime])) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>['imagen_invalid_type']]); return; }
			$ext = $allowed[$mime];
			$uploadDir = __DIR__ . '/../../public/images/promocion/';
			if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
			foreach (['jpg','png'] as $e) { $p = $uploadDir . $id . '.' . $e; if (is_file($p)) @unlink($p); }
			$dest = $uploadDir . $id . '.' . $ext;
			if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) { http_response_code(500); echo json_encode(['status'=>'error','message'=>'upload_failed']); return; }
		}
		header('Content-Type: application/json; charset=utf-8');
		echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo actualizar promocion']);
	}

	 /*
	 * eliminar()
	 * Elimina una promoción y su imagen.
	 * @return void - Retorna JSON
	 */
	public function eliminar() {
		header('Content-Type: application/json; charset=utf-8');
		$id = $_POST['id'] ?? null;
		if (!$id || !ctype_digit($id)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_id']); return; }
		$prom = new PromocionModel();
		$ok = $prom->delete(intval($id));
		if ($ok) {
			$dir = __DIR__ . '/../../public/images/promocion/';
			foreach (['jpg','png'] as $e) {
				$p = $dir . intval($id) . '.' . $e;
				if (is_file($p)) @unlink($p);
			}
		}
		echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo eliminar promocion']);
	}
}

$controller = new PromocionController();
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