<?php

require_once __DIR__ . '/../models/PromocionModel.php';
require_once __DIR__ . '/../models/EventoModel.php';
require_once __DIR__ . '/../models/ProductoModel.php';
require_once __DIR__ . '/../helpers/Functions.php';
require_once __DIR__ . '/../models/MesaModel.php';
require_once __DIR__ . '/../models/ReservaModel.php';

/**
 * Controlador de Usuario
 * Gestiona operaciones de lectura de datos y creación de reservas para usuarios públicos
 */
class UserController
{
    /**
     * Obtiene todas las promociones con sus productos asociados.
     * Incluye información de imágenes si están disponibles.
     * 
     * @return void Envía respuesta JSON con promociones
     */
    public function getPromociones()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $model = new PromocionModel();
            $promociones = $model->getPromocionesConProductos();

            $dir = __DIR__ . '/../../public/images/promocion/';

            // Agregar información de imágenes a cada promoción
            foreach ($promociones as &$p) {
                $id = $p['id_promocion'] ?? null;
                $p['imagen'] = null;

                if ($id !== null) {
                    foreach (['jpg', 'png'] as $ext) {
                        $fname = $id . '.' . $ext;
                        if (is_file($dir . $fname)) {
                            $p['imagen'] = $fname;
                            break;
                        }
                    }
                }
            }

            echo json_encode($promociones);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener promociones']);
        }
    }

    /**
     * Obtiene todos los eventos disponibles.
     * Incluye información de imágenes si están disponibles.
     * 
     * @return void Envía respuesta JSON con eventos
     */
    public function getEventos()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $model = new EventoModel();
            $eventos = $model->getAll();

            $dir = __DIR__ . '/../../public/images/evento/';

            // Agregar información de imágenes a cada evento
            foreach ($eventos as &$ev) {
                $id = $ev['id_evento'] ?? null;
                $ev['imagen'] = null;

                if ($id !== null) {
                    foreach (['jpg', 'png'] as $ext) {
                        $fname = $id . '.' . $ext;
                        if (is_file($dir . $fname)) {
                            $ev['imagen'] = $fname;
                            break;
                        }
                    }
                }
            }

            echo json_encode($eventos);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener eventos']);
        }
    }

    /**
     * Obtiene todos los productos disponibles.
     * 
     * @return void Envía respuesta JSON con productos
     */
    public function getProductos()
    {
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

    /**
     * Obtiene productos filtrados por categoría.
     * Acepta categoría por GET o POST.
     * 
     * @return void Envía respuesta JSON con productos filtrados o error
     */
    public function getProductosPorCategoria()
    {
        header('Content-Type: application/json; charset=utf-8');

        $categoria = $_GET['categoria'] ?? $_POST['categoria'] ?? null;

        // Validar que la categoría sea proporcionada
        if (!$categoria) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Categoría no especificada']);
            return;
        }

        try {
            $model = new ProductoModel();
            $productos = $model->getAll();

            // Filtrar productos por categoría (case-insensitive)
            $productosFiltrados = array_filter($productos, function ($producto) use ($categoria) {
                return strcasecmp($producto['categoria'], $categoria) === 0;
            });

            echo json_encode(array_values($productosFiltrados));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener productos']);
        }
    }

    /**
     * Obtiene las mesas disponibles para una fecha específica.
     * Valida disponibilidad considerando reservas existentes.
     * 
     * @return void Envía respuesta JSON con mesas disponibles o error
     */
    public function getMesasDisponibles()
    {
        header('Content-Type: application/json; charset=utf-8');

        $fecha = $_GET['fecha'] ?? $_POST['fecha'] ?? null;

        // Validar que la fecha sea proporcionada
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
            $mesasReservadas = array_column($reservas, 'id_mesa');

            // Filtrar mesas que no estén reservadas
            $mesasDisponibles = array_filter($mesasActivas, function ($mesa) use ($mesasReservadas) {
                return !in_array($mesa['id_mesa'], $mesasReservadas);
            });

            echo json_encode(array_values($mesasDisponibles));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener mesas disponibles']);
        }
    }

    /**
     * Crea una nueva reserva de mesa.
     * Valida datos, horarios, disponibilidad y gestiona transacciones de base de datos.
     * Crea o actualiza cliente y genera folio único para la reserva.
     * 
     * @return void Envía respuesta JSON con resultado de la operación
     */
    public function crearReserva()
    {
        header('Content-Type: application/json; charset=utf-8');

        // Obtener datos del JSON enviado
        $input = file_get_contents('php://input');
        $datos = json_decode($input, true);

        // Validar campos requeridos
        $required = ['nombre', 'email', 'telefono', 'personas', 'fecha', 'hora', 'id_mesa'];
        foreach ($required as $field) {
            if (empty($datos[$field])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'mensaje' => "Campo '$field' es requerido"]);
                return;
            }
        }

        try {
            // Validar formato de fecha y hora
            $fechaObj = DateTime::createFromFormat('Y-m-d', $datos['fecha']);
            $horaStr = $datos['hora'];

            if (!$fechaObj || !preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $horaStr)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Fecha u hora inválida']);
                return;
            }

            // Validar horario según el día de la semana
            $dow = (int)$fechaObj->format('w');
            $horario = [
                0 => ['open' => true,  'min' => '15:00', 'max' => '21:00'], // Domingo
                1 => ['open' => true,  'min' => '11:00', 'max' => '22:00'], // Lunes
                2 => ['open' => true,  'min' => '10:00', 'max' => '22:00'], // Martes
                3 => ['open' => true,  'min' => '10:30', 'max' => '22:00'], // Miércoles
                4 => ['open' => true,  'min' => '11:00', 'max' => '23:30'], // Jueves
                5 => ['open' => true,  'min' => '11:00', 'max' => '22:00'], // Viernes
                6 => ['open' => false],                                      // Sábado cerrado
            ];

            $cfg = $horario[$dow] ?? ['open' => false];

            if (!$cfg['open']) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Horario no disponible para ese día']);
                return;
            }

            // Convertir hora a minutos para validar rango
            $toMin = function ($hm) {
                [$h, $m] = array_map('intval', explode(':', $hm));
                return $h * 60 + $m;
            };
            $v = $toMin($horaStr);

            if ($v < $toMin($cfg['min']) || $v > $toMin($cfg['max'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'La hora está fuera del horario permitido']);
                return;
            }

            $horaStr = normalizarHora($horaStr);

            // Conectar a base de datos e iniciar transacción
            require_once __DIR__ . '/../models/Conexion.php';
            $db = Conexion::conectar();
            $db->beginTransaction();

            // Buscar o crear cliente
            $sqlCliente = "SELECT id_cliente FROM cliente WHERE correo = ? LIMIT 1";
            $stmtCliente = $db->prepare($sqlCliente);
            $stmtCliente->execute([$datos['email']]);
            $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                $idCliente = $cliente['id_cliente'];
                // Actualizar datos del cliente existente
                $sqlUpdate = "UPDATE cliente SET nombre = ?, telefono = ? WHERE id_cliente = ?";
                $stmtUpdate = $db->prepare($sqlUpdate);
                $stmtUpdate->execute([$datos['nombre'], $datos['telefono'], $idCliente]);
            } else {
                // Crear nuevo cliente
                $sqlInsert = "INSERT INTO cliente (nombre, correo, telefono) VALUES (?, ?, ?)";
                $stmtInsert = $db->prepare($sqlInsert);
                $stmtInsert->execute([$datos['nombre'], $datos['email'], $datos['telefono']]);
                $idCliente = $db->lastInsertId();
            }

            // Verificar que la mesa no esté reservada para esa fecha
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

            // Crear reserva con folio único
            $folio = 'RES-' . strtoupper(substr(uniqid(), -8));
            $sqlReserva = "INSERT INTO reserva (folio, id_cliente, id_evento, fecha, hora, num_personas, id_mesa, estado) 
                          VALUES (?, ?, NULL, ?, ?, ?, ?, 'pendiente')";
            $stmtReserva = $db->prepare($sqlReserva);
            $stmtReserva->execute([
                $folio,
                $idCliente,
                $datos['fecha'],
                $horaStr,
                $datos['personas'],
                $datos['id_mesa']
            ]);

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

// Instanciar el controlador y ejecutar la acción solicitada
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