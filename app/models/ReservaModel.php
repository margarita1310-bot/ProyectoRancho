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
}
?>
