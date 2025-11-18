<?php
 /*
 * EventoController.php
 * 
 * Controlador CRUD de Eventos con gestión de imágenes.
 * Crea, lee, actualiza y elimina eventos. Las imágenes se guardan en public/images/evento/
 * 
 * Acciones:
 * - index: GET - Lista todos los eventos
 * - guardar: POST - Crea nuevo evento (multipart/form-data con imagen opcional)
 * - obtener: POST - Obtiene datos de un evento (JSON)
 * - actualizar: POST - Actualiza un evento (multipart/form-data con imagen opcional)
 * - eliminar: POST - Elimina un evento y su imagen (JSON)
 * 
 * Tabla de base de datos: evento (id_evento, nombre, descripcion, fecha, hora_inicio, hora_fin, imagen)
 * 
 * Requiere: Autenticación de administrador (ensureAdmin())
 * 
 * Validación de imágenes:
 * - Opcional en crear y actualizar
 * - Máximo 2MB
 * - Tipos permitidos: JPEG, PNG (validados por MIME type)
 * - Nombres únicos: {timestamp}_{random6bytes}.{ext}
 * - Directorio: public/images/eventos/
 */

require_once __DIR__ . '/../models/EventoModel.php';
require_once __DIR__ . '/Auth.php';

ensureAdmin();

class EventoController {
	 /*
	 * index()
	 * Muestra lista de eventos.
	 * @return void - Incluye vista EventoView.php
	 */
	public function index() {
		$ev = new EventoModel();
		$evento = $ev->getAll();
		require_once __DIR__ . '/../../app/views/admin/EventoView.php';
	}

	/**
	 * guardar()
	 * Crea un nuevo evento en la base de datos con imagen opcional.
	 * @return void - Retorna JSON
	 */
	public function guardar() {
		header('Content-Type: application/json; charset=utf-8');
		$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
		$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
		$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : null;
		$hora_inicio = isset($_POST['hora_inicio']) ? trim($_POST['hora_inicio']) : null;
		$hora_fin = isset($_POST['hora_fin']) ? trim($_POST['hora_fin']) : null;

		// Validar parámetros de texto
		$errors = [];
		if (!$nombre) $errors[] = 'nombre_required';
		if (!$fecha || !DateTime::createFromFormat('Y-m-d', $fecha)) $errors[] = 'fecha_invalid';
		if (!$hora_inicio || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora_inicio)) $errors[] = 'hora_inicio_invalid';
		if ($hora_fin && !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora_fin)) $errors[] = 'hora_fin_invalid';

		if (!empty($errors)) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>$errors]); return; }

		// Procesar imagen si se subió (opcional)
		$imagenNombre = null;
		if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
			$maxSize = 2 * 1024 * 1024;
			$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
			if ($_FILES['imagen']['size'] > $maxSize) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>['imagen_too_large']]); return; }
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
			finfo_close($finfo);
			if (!isset($allowed[$mime])) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>['imagen_invalid_type']]); return; }
			$ext = $allowed[$mime];
			$uploadDir = __DIR__ . '/../../public/images/evento/';
			if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
			$imagenNombre = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
			$dest = $uploadDir . $imagenNombre;
			if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) { http_response_code(500); echo json_encode(['status'=>'error','message'=>'upload_failed']); return; }
		}

		// Crear evento
		$ev = new EventoModel();
		$ok = $ev->create($nombre, $descripcion, $fecha, $hora_inicio, $hora_fin, $imagenNombre);
		header('Content-Type: application/json; charset=utf-8');
		echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo crear evento']);
	}

	 /*
	 * obtener()
	 * Obtiene datos de un evento por ID.
	 * @return void - Retorna JSON con datos
	 */
	public function obtener() {
		$id = $_POST['id'] ?? null;
		if (!$id || !ctype_digit($id)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_id']); return; }
		header('Content-Type: application/json; charset=utf-8');
		$ev = new EventoModel();
		echo json_encode($ev->getById(intval($id)));
	}

	 /*
	 * actualizar()
	 * Actualiza evento con nueva imagen opcional.
	 * @return void - Retorna JSON
	 */
	
	public function actualizar() {
		header('Content-Type: application/json; charset=utf-8');
		$id = isset($_POST['id']) ? trim($_POST['id']) : null;
		$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
		$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
		$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : null;
		$hora_inicio = isset($_POST['hora_inicio']) ? trim($_POST['hora_inicio']) : null;
		$hora_fin = isset($_POST['hora_fin']) ? trim($_POST['hora_fin']) : null;

		// Validar parámetros
		$errors = [];
		if (!$id || !ctype_digit($id)) $errors[] = 'id_invalid';
		if (!$nombre) $errors[] = 'nombre_required';
		if (!$fecha || !DateTime::createFromFormat('Y-m-d', $fecha)) $errors[] = 'fecha_invalid';
		if (!$hora_inicio || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora_inicio)) $errors[] = 'hora_inicio_invalid';
		if ($hora_fin && !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/', $hora_fin)) $errors[] = 'hora_fin_invalid';

		if (!empty($errors)) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>$errors]); return; }

		// Procesar imagen si se subió (opcional)
		$imagenNombre = null;
		if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
			$maxSize = 2 * 1024 * 1024;
			$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
			if ($_FILES['imagen']['size'] > $maxSize) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>['imagen_too_large']]); return; }
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
			finfo_close($finfo);
			if (!isset($allowed[$mime])) { http_response_code(400); echo json_encode(['status'=>'error','errors'=>['imagen_invalid_type']]); return; }
			$ext = $allowed[$mime];
			$uploadDir = __DIR__ . '/../../public/images/evento/';
			if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
			$imagenNombre = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
			$dest = $uploadDir . $imagenNombre;
			if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) { http_response_code(500); echo json_encode(['status'=>'error','message'=>'upload_failed']); return; }
		}

		// Actualizar evento
		$ev = new EventoModel();
		// Obtener anterior imagen (para eliminar si se actualizó)
		$existing = $ev->getById(intval($id));
		$oldImagen = $existing['imagen'] ?? null;
		$ok = $ev->update($id, $nombre, $descripcion, $fecha, $hora_inicio, $hora_fin, $imagenNombre);
		// Si se actualizó y existía imagen anterior, eliminarla
		if ($ok && $imagenNombre && $oldImagen) {
			$oldPath = __DIR__ . '/../../public/images/evento/' . $oldImagen;
			if (is_file($oldPath)) @unlink($oldPath);
		}
		header('Content-Type: application/json; charset=utf-8');
		echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo actualizar evento']);
	}

	 /*
	 * eliminar()
	 * Elimina evento y su imagen.
	 * @return void - Retorna JSON
	 */
	public function eliminar() {
		header('Content-Type: application/json; charset=utf-8');
		$id = $_POST['id'] ?? null;
		if (!$id || !ctype_digit($id)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'missing_id']); return; }
		$ev = new EventoModel();
		// Antes de eliminar, obtener imagen para borrarla del filesystem
		$existing = $ev->getById(intval($id));
		$oldImagen = $existing['imagen'] ?? null;
		$ok = $ev->delete(intval($id));
		if ($ok && $oldImagen) {
			$oldPath = __DIR__ . '/../../public/images/evento/' . $oldImagen;
			if (is_file($oldPath)) @unlink($oldPath);
		}
		echo $ok ? json_encode(['status'=>'ok']) : json_encode(['status'=>'error','message'=>'no se pudo eliminar evento']);
	}
}

$controller = new EventoController();
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