<?php
 /*
 * ReservaModel.php
 * 
 * Modelo para gestionar reservas de clientes.
 * Obtiene, filtra por fecha, confirma (con asignación opcional de mesa) y elimina reservas.
 * Al confirmar reserva, actualiza automáticamente el estado de la mesa.
 * 
 * Tabla: reserva (id_reserva, id_cliente, id_evento, id_mesa, fecha, hora, num_personas, estado, fecha_creacion, folio)
 * 
 * Estados de reserva: 'pendiente', 'confirmada', 'cancelada'.
 * 
 * Métodos:
 * - getAll(): Retorna todas las reservas
 * - getPending(): Retorna reservas pendientes
 * - getByDate($fecha): Filtra reservas por fecha
 * - getById($id): Obtiene una reserva por ID
 * - confirm($id[, $idMesa]): Confirma una reserva y actualiza estado de mesa
 * - delete($id): Elimina una reserva y libera la mesa
 */

require_once 'Conexion.php';
require_once 'MesaModel.php';

class ReservaModel {
     /*
     * getAll()
     * Retorna todas las reservas ordenadas DESC por ID.
     * Incluye JOIN con tabla cliente para obtener nombre completo.
     * @return array - Array de reservas con datos del cliente
     */
    public function getAll() {
        $db = Conexion::conectar();
        $sql = "SELECT r.*, CONCAT(c.nombre, ' ') as nombre
                FROM reserva r
                LEFT JOIN cliente c ON r.id_cliente = c.id_cliente
                ORDER BY r.id_reserva DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     /*
     * getPending()
     * Retorna solo las reservas con estado='pendiente'.
     * @return array - Array de reservas pendientes ordenadas por fecha y hora
     */
    public function getPending() {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE estado = 'pendiente' ORDER BY fecha, hora");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     /*
     * getByDate($fecha)
     * Filtra reservas de una fecha específica.
     * @return array - Array de reservas de esa fecha ordenadas por hora
     */
    public function getByDate($fecha) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE fecha = ? ORDER BY hora");
        $stmt->execute([$fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     /*
     * getById($id)
     * Obtiene una reserva por ID.
     * @return array|false - Datos de la reserva o false
     */
    public function getById($id) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE id_reserva = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     /*
     * confirm($id[, $idMesa])
     * Marca una reserva como confirmada.
     * Si se proporciona id_mesa (2do parámetro), asigna la mesa y actualiza su estado a 'Ocupada'.
     * 
     * @param int $id - ID de la reserva
     * @param int $idMesa - (opcional) ID de la mesa a asignar
     * @return bool - true si se confirmó, false si hubo error
     */
    public function confirm($id) {
        $db = Conexion::conectar();
        
        try {
            $db->beginTransaction();
            
            // Obtener datos de la reserva
            $reserva = $this->getById($id);
            if (!$reserva) {
                $db->rollBack();
                return false;
            }
            
            // Si se pasa segundo parámetro (id_mesa), actualizar también la mesa
            if (func_num_args() >= 2) {
                $idMesa = func_get_arg(1);
                
                // Actualizar reserva con id_mesa
                $stmt = $db->prepare("UPDATE reserva SET estado = 'confirmada', id_mesa = ? WHERE id_reserva = ?");
                $stmt->execute([$idMesa, $id]);
                
                // Actualizar estado de la mesa a 'Ocupada' y asignar cliente
                $mesaModel = new MesaModel();
                $mesaModel->actualizarEstado($idMesa, 'Ocupada', $reserva['id_cliente']);
            } else {
                $stmt = $db->prepare("UPDATE reserva SET estado = 'confirmada' WHERE id_reserva = ?");
                $stmt->execute([$id]);
            }
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

     /*
     * delete($id)
     * 
     * Elimina una reserva (cancelar).
     * Si la reserva tenía una mesa asignada, libera la mesa (estado='Disponible').
     * 
     * @param int $id - ID de la reserva
     * @return bool - true si se eliminó, false si hubo error
     */
    public function delete($id) {
        $db = Conexion::conectar();
        
        try {
            $db->beginTransaction();
            
            // Obtener datos de la reserva antes de eliminar
            $reserva = $this->getById($id);
            if ($reserva && $reserva['id_mesa']) {
                // Liberar la mesa si estaba asignada
                $mesaModel = new MesaModel();
                $mesaModel->liberarMesa($reserva['id_mesa']);
            }
            
            // Eliminar la reserva
            $stmt = $db->prepare("DELETE FROM reserva WHERE id_reserva = ?");
            $stmt->execute([$id]);
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

     /*
     * getMesasActivasYDisponibles($fecha)
     * 
     * Obtiene las mesas disponibles para una fecha específica según la nueva lógica:
     * 1. Consulta la cantidad de mesas permitidas desde 'disponibilidad'
     * 2. Obtiene las primeras N mesas activas del catálogo 'mesa'
     * 3. Filtra las que ya están reservadas para esa fecha
     * 4. Retorna la lista de mesas libres
     * 
     * @param string $fecha - Fecha en formato YYYY-MM-DD
     * @return array - Array de mesas disponibles (id_mesa, numero, capacidad)
     */
    public function getMesasActivasYDisponibles($fecha) {
        $db = Conexion::conectar();
        
        try {
            // Paso 1: Obtener cantidad de mesas permitidas para la fecha
            error_log("[getMesasActivasYDisponibles] Consultando disponibilidad para fecha: $fecha");
            
            require_once 'DisponibilidadModel.php';
            $dispModel = new DisponibilidadModel();
            $disponibilidad = $dispModel->getByDate($fecha);
            
            if (!$disponibilidad || !isset($disponibilidad['cantidad'])) {
                error_log("[getMesasActivasYDisponibles] No hay disponibilidad configurada para $fecha");
                return []; // No hay disponibilidad configurada para esta fecha
            }
            
            $cantidadPermitida = (int)$disponibilidad['cantidad'];
            error_log("[getMesasActivasYDisponibles] Cantidad permitida: $cantidadPermitida");
            
            // Paso 2: Obtener las primeras N mesas activas del catálogo
            // IMPORTANTE: Convertir $cantidadPermitida a int para evitar problemas con LIMIT
            $sql = "SELECT id_mesa, numero 
                    FROM mesa 
                    WHERE activa = 1 
                    ORDER BY numero ASC 
                    LIMIT " . (int)$cantidadPermitida;
            $stmt = $db->query($sql);
            $mesasActivas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("[getMesasActivasYDisponibles] Mesas activas encontradas: " . count($mesasActivas));
            
            if (empty($mesasActivas)) {
                error_log("[getMesasActivasYDisponibles] No hay mesas activas en el catálogo");
                return []; // No hay mesas activas en el catálogo
            }
            
            // Paso 3: Obtener IDs de mesas ya reservadas para esa fecha
            $sql = "SELECT id_mesa 
                    FROM reserva 
                    WHERE fecha = ? 
                    AND estado IN ('pendiente', 'confirmada') 
                    AND id_mesa IS NOT NULL";
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha]);
            $mesasReservadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            error_log("[getMesasActivasYDisponibles] Mesas ya reservadas: " . count($mesasReservadas));
            
            // Paso 4: Filtrar las mesas ocupadas
            $mesasDisponibles = array_filter($mesasActivas, function($mesa) use ($mesasReservadas) {
                return !in_array($mesa['id_mesa'], $mesasReservadas);
            });
            
            $resultado = array_values($mesasDisponibles); // Reindexar array
            error_log("[getMesasActivasYDisponibles] Mesas disponibles finales: " . count($resultado));
            
            return $resultado;
        } catch (Exception $e) {
            error_log("[getMesasActivasYDisponibles] Error: " . $e->getMessage());
            throw $e;
        }
    }

     /*
     * getReservasPorFechaConMesas($fecha)
     * 
     * Obtiene todas las mesas disponibles para una fecha específica
     * y las combina con las reservas existentes.
     * Retorna un array donde cada fila representa una mesa, con o sin reserva.
     * 
     * @param string $fecha - Fecha en formato YYYY-MM-DD
     * @return array - Array de mesas con información de reserva (si existe)
     */
    public function getReservasPorFechaConMesas($fecha) {
        $db = Conexion::conectar();
        
        try {
            // Paso 1: Obtener disponibilidad para la fecha
            require_once 'DisponibilidadModel.php';
            $dispModel = new DisponibilidadModel();
            $disponibilidad = $dispModel->getByDate($fecha);
            
            if (!$disponibilidad || !isset($disponibilidad['cantidad'])) {
                return ['disponibilidad' => false, 'mesas' => []];
            }
            
            $cantidadPermitida = (int)$disponibilidad['cantidad'];
            
            // Paso 2: Obtener las primeras N mesas activas
            $sql = "SELECT id_mesa, numero 
                    FROM mesa 
                    WHERE activa = 1 
                    ORDER BY numero ASC 
                    LIMIT " . (int)$cantidadPermitida;
            $stmt = $db->query($sql);
            $mesasActivas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($mesasActivas)) {
                return ['disponibilidad' => true, 'mesas' => []];
            }
            
            // Paso 3: Obtener reservas para esa fecha
            $sql = "SELECT r.*, c.nombre as cliente_nombre
                    FROM reserva r
                    LEFT JOIN cliente c ON r.id_cliente = c.id_cliente
                    WHERE r.fecha = ? 
                    AND r.id_mesa IS NOT NULL";
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha]);
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Crear un mapa de reservas por id_mesa
            $reservasPorMesa = [];
            foreach ($reservas as $reserva) {
                $reservasPorMesa[$reserva['id_mesa']] = $reserva;
            }
            
            // Paso 4: Combinar mesas con reservas
            $resultado = [];
            foreach ($mesasActivas as $mesa) {
                $fila = [
                    'id_mesa' => $mesa['id_mesa'],
                    'numero_mesa' => $mesa['numero'],
                    'tiene_reserva' => isset($reservasPorMesa[$mesa['id_mesa']]),
                    'id_reserva' => null,
                    'folio' => '-',
                    'cliente_nombre' => '-',
                    'hora' => '-',
                    'num_personas' => '-',
                    'estado' => null
                ];
                
                // Si hay reserva para esta mesa, agregar los datos
                if (isset($reservasPorMesa[$mesa['id_mesa']])) {
                    $reserva = $reservasPorMesa[$mesa['id_mesa']];
                    $fila['id_reserva'] = $reserva['id_reserva'];
                    $fila['folio'] = $reserva['folio'] ?? '-';
                    $fila['cliente_nombre'] = $reserva['cliente_nombre'] ?? '-';
                    $fila['hora'] = $reserva['hora'] ?? '-';
                    $fila['num_personas'] = $reserva['num_personas'] ?? '-';
                    $fila['estado'] = $reserva['estado'];
                }
                
                $resultado[] = $fila;
            }
            
            return ['disponibilidad' => true, 'mesas' => $resultado];
        } catch (Exception $e) {
            error_log("[getReservasPorFechaConMesas] Error: " . $e->getMessage());
            throw $e;
        }
    }
}
?>
