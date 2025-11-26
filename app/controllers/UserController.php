<?php
 /*
 * UserController.php
 * 
 * Controlador público para el área de usuarios (sin autenticación requerida).
 * Proporciona endpoints para obtener promociones, eventos y productos del menú.
 * 
 * Acciones:
 * - getPromociones: GET/POST - Retorna promociones activas en JSON
 * - getEventos: GET/POST - Retorna eventos próximos en JSON
 * - getProductos: GET/POST - Retorna todos los productos en JSON
 * - getProductosPorCategoria: GET/POST - Retorna productos filtrados por categoría
 * 
 * NO requiere autenticación (público)
 */

require_once __DIR__ . '/../models/PromocionModel.php';
require_once __DIR__ . '/../models/EventoModel.php';
require_once __DIR__ . '/../models/ProductoModel.php';
require_once __DIR__ . '/../models/MesaModel.php';
require_once __DIR__ . '/../models/ReservaModel.php';

class UserController {
    
    /*
     * getPromociones()
     * Retorna todas las promociones en formato JSON.
     * @return void - Envía JSON al cliente
     */
    public function getPromociones() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $model = new PromocionModel();
            $promociones = $model->getPromocionesConProductos();
            // Adjuntar nombre de imagen desde filesystem si existe (id.{jpg|png})
            $dir = __DIR__ . '/../../public/images/promocion/';
            foreach ($promociones as &$p) {
                $id = $p['id_promocion'] ?? null;
                $p['imagen'] = null;
                if ($id !== null) {
                    foreach (['jpg','png'] as $ext) {
                        $fname = $id . '.' . $ext;
                        if (is_file($dir . $fname)) { $p['imagen'] = $fname; break; }
                    }
                }
            }
            echo json_encode($promociones);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener promociones']);
        }
    }

    /*
     * getEventos()
     * Retorna todos los eventos en formato JSON.
     * @return void - Envía JSON al cliente
     */
    public function getEventos() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $model = new EventoModel();
            $eventos = $model->getAll();
            // Adjuntar nombre de imagen desde filesystem si existe (id.{jpg|png})
            $dir = __DIR__ . '/../../public/images/evento/';
            foreach ($eventos as &$ev) {
                $id = $ev['id_evento'] ?? null;
                $ev['imagen'] = null;
                if ($id !== null) {
                    foreach (['jpg','png'] as $ext) {
                        $fname = $id . '.' . $ext;
                        if (is_file($dir . $fname)) { $ev['imagen'] = $fname; break; }
                    }
                }
            }
            echo json_encode($eventos);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener eventos']);
        }
    }

    /*
     * getProductos()
     * Retorna todos los productos del menú en formato JSON.
     * @return void - Envía JSON al cliente
     */
    public function getProductos() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $model = new ProductoModel();
            $productos = $model->getAll();
            echo json_encode($productos);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener productos']);
        }
    }

    /*
     * getProductosPorCategoria()
     * Retorna productos filtrados por categoría.
     * @return void - Envía JSON al cliente
     */
    public function getProductosPorCategoria() {
        header('Content-Type: application/json; charset=utf-8');
        
        $categoria = $_GET['categoria'] ?? $_POST['categoria'] ?? null;
        
        if (!$categoria) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Categoría no especificada']);
            return;
        }

        try {
            $model = new ProductoModel();
            $productos = $model->getAll();
            
            // Filtrar por categoría (case-insensitive)
            $productosFiltrados = array_filter($productos, function($producto) use ($categoria) {
                return strcasecmp($producto['categoria'], $categoria) === 0;
            });
            
            echo json_encode(array_values($productosFiltrados));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener productos']);
        }
    }

    /*
     * getMesasDisponibles()
     * Retorna las mesas disponibles para una fecha específica.
     * Una mesa está disponible si está activa y no tiene una reserva para esa fecha.
     * @return void - Envía JSON al cliente
     */
    public function getMesasDisponibles() {
        header('Content-Type: application/json; charset=utf-8');
        
        $fecha = $_GET['fecha'] ?? $_POST['fecha'] ?? null;
        
        if (!$fecha) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Fecha no especificada']);
            return;
        }

        try {
            $mesaModel = new MesaModel();
            $reservaModel = new ReservaModel();
            
            // Obtener todas las mesas activas
            $mesasActivas = $mesaModel->getMesasActivas();
            
            // Obtener reservas para la fecha especificada
            $reservas = $reservaModel->getByDate($fecha);
            
            // Crear array de IDs de mesas reservadas
            $mesasReservadas = array_column($reservas, 'id_mesa');
            
            // Filtrar mesas disponibles (activas y no reservadas)
            $mesasDisponibles = array_filter($mesasActivas, function($mesa) use ($mesasReservadas) {
                return !in_array($mesa['id_mesa'], $mesasReservadas);
            });
            
            echo json_encode(array_values($mesasDisponibles));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener mesas disponibles']);
        }
    }

    /*
     * crearReserva()
     * Crea una nueva reserva desde el área pública.
     * Requiere: nombre, email, telefono, personas, fecha, hora, id_mesa
     * @return void - Envía JSON al cliente
     */
    public function crearReserva() {
        header('Content-Type: application/json; charset=utf-8');
        
        // Leer datos JSON del body
        $input = file_get_contents('php://input');
        $datos = json_decode($input, true);
        
        // Validar datos requeridos
        $required = ['nombre', 'email', 'telefono', 'personas', 'fecha', 'hora', 'id_mesa'];
        foreach ($required as $field) {
            if (empty($datos[$field])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'mensaje' => "Campo '$field' es requerido"]);
                return;
            }
        }
        
        try {
            // Validar hora contra horario del lugar por día
            $fechaObj = DateTime::createFromFormat('Y-m-d', $datos['fecha']);
            $horaStr = $datos['hora'];
            if (!$fechaObj || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $horaStr)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Fecha u hora inválida']);
                return;
            }
            $dow = (int)$fechaObj->format('w'); // 0=domingo..6=sábado
            $horario = [
                0 => ['open' => true,  'min' => '15:00', 'max' => '21:00'], // Domingo
                1 => ['open' => true,  'min' => '11:00', 'max' => '22:00'], // Lunes
                2 => ['open' => true,  'min' => '10:00', 'max' => '22:00'], // Martes
                3 => ['open' => true,  'min' => '10:30', 'max' => '22:00'], // Miércoles
                4 => ['open' => true,  'min' => '11:00', 'max' => '23:30'], // Jueves
                5 => ['open' => true,  'min' => '11:00', 'max' => '22:00'], // Viernes
                6 => ['open' => false],                                  // Sábado cerrado
            ];
            $cfg = $horario[$dow] ?? ['open' => false];
            if (!$cfg['open']) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Horario no disponible para ese día']);
                return;
            }
            $toMin = function($hm){ [$h,$m] = array_map('intval', explode(':', $hm)); return $h*60 + $m; };
            $v = $toMin($horaStr);
            if ($v < $toMin($cfg['min']) || $v > $toMin($cfg['max'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'La hora está fuera del horario permitido']);
                return;
            }

            require_once __DIR__ . '/../models/Conexion.php';
            $db = Conexion::conectar();
            
            // Iniciar transacción
            $db->beginTransaction();
            
            // 1. Crear o buscar cliente
            $sqlCliente = "SELECT id_cliente FROM cliente WHERE correo = ? LIMIT 1";
            $stmtCliente = $db->prepare($sqlCliente);
            $stmtCliente->execute([$datos['email']]);
            $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);
            
            if ($cliente) {
                $idCliente = $cliente['id_cliente'];
                // Actualizar datos del cliente
                $sqlUpdate = "UPDATE cliente SET nombre = ?, telefono = ? WHERE id_cliente = ?";
                $stmtUpdate = $db->prepare($sqlUpdate);
                $stmtUpdate->execute([$datos['nombre'], $datos['telefono'], $idCliente]);
            } else {
                // Insertar nuevo cliente
                $sqlInsert = "INSERT INTO cliente (nombre, correo, telefono) VALUES (?, ?, ?)";
                $stmtInsert = $db->prepare($sqlInsert);
                $stmtInsert->execute([$datos['nombre'], $datos['email'], $datos['telefono']]);
                $idCliente = $db->lastInsertId();
            }
            
            // 2. Verificar que la mesa esté disponible
            $sqlVerificar = "SELECT COUNT(*) as total FROM reserva 
                            WHERE fecha = ? AND id_mesa = ? AND estado IN ('pendiente', 'confirmada')";
            $stmtVerificar = $db->prepare($sqlVerificar);
            $stmtVerificar->execute([$datos['fecha'], $datos['id_mesa']]);
            $result = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total'] > 0) {
                $db->rollBack();
                http_response_code(409);
                echo json_encode(['status' => 'error', 'mensaje' => 'La mesa seleccionada ya está reservada para esta fecha']);
                return;
            }
            
            // 3. Generar folio único
            $folio = 'RES-' . strtoupper(substr(uniqid(), -8));
            
            // 4. Crear la reserva
            $sqlReserva = "INSERT INTO reserva (folio, id_cliente, id_evento, fecha, hora, num_personas, id_mesa, estado) 
                          VALUES (?, ?, NULL, ?, ?, ?, ?, 'pendiente')";
            $stmtReserva = $db->prepare($sqlReserva);
            $stmtReserva->execute([
                $folio,
                $idCliente,
                $datos['fecha'],
                $datos['hora'],
                $datos['personas'],
                $datos['id_mesa']
            ]);
            
            // Confirmar transacción
            $db->commit();
            
            echo json_encode([
                'status' => 'ok',
                'mensaje' => 'Reserva creada exitosamente',
                'folio' => $folio
            ]);
            
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['status' => 'error', 'mensaje' => 'Error al crear reserva: ' . $e->getMessage()]);
        }
    }
}

// Enrutamiento
$controller = new UserController();
$action = $_GET['action'] ?? 'getPromociones';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Acción no encontrada']);
}
?>
