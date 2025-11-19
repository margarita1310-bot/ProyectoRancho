<?php
/**
 * MesaModel.php
 * 
 * Modelo para gestionar mesas del restaurante.
 * Permite activar/desactivar mesas, consultar su estado y obtener información de reservas asociadas.
 * 
 * Tabla: mesa (id_mesa, numero, activa, id_cliente, estado)
 * Estados: 'Disponible', 'Ocupada'
 * 
 * Métodos:
 * - activarMesas($cantidad): Activa mesas desde 1 hasta $cantidad
 * - desactivarTodasMesas(): Desactiva todas las mesas
 * - getMesasActivas(): Obtiene todas las mesas activas con datos del cliente
 * - getMesaById($id): Obtiene una mesa específica
 * - actualizarEstado($id, $estado, $idCliente): Actualiza estado y cliente de una mesa
 * - liberarMesa($id): Libera una mesa (disponible, sin cliente)
 */

require_once 'Conexion.php';

class MesaModel {
    /**
     * activarMesas($cantidad)
     * 
     * Activa las primeras $cantidad mesas (numero 1 hasta $cantidad).
     * Primero desactiva todas y luego activa las necesarias.
     * Reinicia el estado a 'Disponible' y elimina cliente asignado.
     * 
     * NOTA: Este método NO usa transacciones propias porque puede ser llamado
     * desde dentro de otras transacciones (ej: DisponibilidadModel).
     * 
     * @param int $cantidad - Número de mesas a activar
     * @return bool - true si se activaron correctamente
     */
    public function activarMesas($cantidad) {
        $db = Conexion::conectar();
        
        try {
            // Desactivar todas las mesas primero
            $sql = "UPDATE mesa SET activa = 0, estado = 'Disponible', id_cliente = NULL";
            $db->exec($sql);
            
            // Activar solo las mesas del 1 hasta $cantidad
            $sql = "UPDATE mesa SET activa = 1, estado = 'Disponible', id_cliente = NULL WHERE numero >= 1 AND numero <= ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$cantidad]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * desactivarTodasMesas()
     * 
     * Desactiva todas las mesas del sistema.
     * 
     * @return bool - true si se desactivaron correctamente
     */
    public function desactivarTodasMesas() {
        $db = Conexion::conectar();
        $sql = "UPDATE mesa SET activa = 0, estado = 'Disponible', id_cliente = NULL";
        return $db->exec($sql) !== false;
    }

    /**
     * getMesasActivas()
     * 
     * Obtiene todas las mesas activas con información del cliente asociado.
     * JOIN con tabla cliente para obtener nombre y otros datos.
     * 
     * @return array - Array de mesas activas con datos del cliente
     */
    public function getMesasActivas() {
        $db = Conexion::conectar();
        $sql = "SELECT m.*, c.nombre, c.telefono
                FROM mesa m
                LEFT JOIN cliente c ON m.id_cliente = c.id_cliente
                WHERE m.activa = 1
                ORDER BY m.numero ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * getMesaById($id)
     * 
     * Obtiene una mesa específica por su ID.
     * 
     * @param int $id - ID de la mesa
     * @return array|false - Datos de la mesa o false
     */
    public function getMesaById($id) {
        $db = Conexion::conectar();
        $sql = "SELECT m.*, c.nombre
                FROM mesa m
                LEFT JOIN cliente c ON m.id_cliente = c.id_cliente
                WHERE m.id_mesa = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * actualizarEstado($id, $estado, $idCliente)
     * 
     * Actualiza el estado de una mesa y asigna un cliente.
     * 
     * @param int $id - ID de la mesa
     * @param string $estado - 'Disponible' o 'Ocupada'
     * @param int|null $idCliente - ID del cliente a asignar (null para liberar)
     * @return bool - true si se actualizó correctamente
     */
    public function actualizarEstado($id, $estado, $idCliente = null) {
        $db = Conexion::conectar();
        $sql = "UPDATE mesa SET estado = ?, id_cliente = ? WHERE id_mesa = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$estado, $idCliente, $id]);
    }

    /**
     * liberarMesa($id)
     * 
     * Libera una mesa (estado Disponible, sin cliente).
     * 
     * @param int $id - ID de la mesa
     * @return bool - true si se liberó correctamente
     */
    public function liberarMesa($id) {
        return $this->actualizarEstado($id, 'Disponible', null);
    }

    /**
     * tieneReservasActivas($fecha)
     * 
     * Verifica si hay reservas confirmadas o pendientes para una fecha.
     * 
     * @param string $fecha - Fecha en formato YYYY-MM-DD
     * @return bool - true si hay reservas activas
     */
    public function tieneReservasActivas($fecha) {
        $db = Conexion::conectar();
        $sql = "SELECT COUNT(*) as total FROM reserva 
                WHERE fecha = ? AND estado IN ('pendiente', 'confirmada')";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
}
?>
