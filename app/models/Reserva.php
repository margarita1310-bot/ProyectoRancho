<?php
/**
 * Reserva.php
 * 
 * Modelo para gestionar reservas de clientes.
 * Obtiene, filtra por fecha, confirma (con asignación opcional de mesa) y elimina reservas.
 * 
 * Tabla: reserva (id_reserva, id_cliente, id_evento, fecha, hora, num_personas, folio, estado, codigo_conf, fecha_creacion, mesa)
 * 
 * Estados de reserva: 'pendiente', 'confirmada'
 * 
 * Métodos:
 * - getAll(): Retorna todas las reservas
 * - getPending(): Retorna reservas pendientes
 * - getByDate($fecha): Filtra reservas por fecha
 * - getById($id): Obtiene una reserva por ID
 * - confirm($id[, $mesa]): Confirma una reserva (mesa es parámetro variádico)
 * - delete($id): Elimina una reserva
 */

require_once 'Conexion.php';

class Reserva {
    /**
     * getAll()
     * 
     * Retorna todas las reservas ordenadas DESC por ID.
     * 
     * @return array - Array de reservas
     */
    public function getAll() {
        $db = Conexion::conectar();
        $stmt = $db->query("SELECT * FROM reserva ORDER BY id_reserva DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * getPending()
     * 
     * Retorna solo las reservas con estado='pendiente'.
     * 
     * @return array - Array de reservas pendientes ordenadas por fecha y hora
     */
    public function getPending() {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE estado = 'pendiente' ORDER BY fecha, hora");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * getByDate($fecha)
     * 
     * Filtra reservas de una fecha específica.
     * 
     * @param string $fecha - Fecha en formato YYYY-MM-DD
     * @return array - Array de reservas de esa fecha ordenadas por hora
     */
    public function getByDate($fecha) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE fecha = ? ORDER BY hora");
        $stmt->execute([$fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * getById($id)
     * 
     * Obtiene una reserva por ID.
     * 
     * @param int $id - ID de la reserva
     * @return array|false - Datos de la reserva o false
     */
    public function getById($id) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE id_reserva = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * confirm($id[, $mesa])
     * 
     * Marca una reserva como confirmada.
     * Si se proporciona mesa (2do parámetro variádico), también asigna el número de mesa.
     * 
     * Flujo:
     * - Sin mesa: UPDATE ... SET estado='confirmada' WHERE id_reserva=?
     * - Con mesa: UPDATE ... SET estado='confirmada', mesa=? WHERE id_reserva=?
     * 
     * @param int $id - ID de la reserva
     * @param int|null $mesa - (Opcional) Número de mesa a asignar
     * @return bool - true si se confirmó, false si hubo error
     */
    public function confirm($id) {
        $db = Conexion::conectar();
        // Si se pasa segundo parámetro, actualizar también el número de mesa
        if (func_num_args() >= 2) {
            $mesa = func_get_arg(1);
            $stmt = $db->prepare("UPDATE reserva SET estado = 'confirmada', mesa = ? WHERE id_reserva = ?");
            return $stmt->execute([$mesa, $id]);
        }
        $stmt = $db->prepare("UPDATE reserva SET estado = 'confirmada' WHERE id_reserva = ?");
        return $stmt->execute([$id]);
    }

    /**
     * delete($id)
     * 
     * Elimina una reserva.
     * 
     * @param int $id - ID de la reserva
     * @return bool - true si se eliminó, false si hubo error
     */
    public function delete($id) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("DELETE FROM reserva WHERE id_reserva = ?");
        return $stmt->execute([$id]);
    }
}

?>
