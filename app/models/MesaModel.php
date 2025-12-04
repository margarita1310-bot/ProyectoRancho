<?php
require_once 'Conexion.php';
class MesaModel {
    public function activarMesas($cantidad) {
        $db = Conexion::conectar();
        try {
            $sql = "UPDATE mesa SET activa = 0, estado = 'Disponible', id_cliente = NULL";
            $db->exec($sql);
            $sql = "UPDATE mesa SET activa = 1, estado = 'Disponible', id_cliente = NULL WHERE numero >= 1 AND numero <= ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$cantidad]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function desactivarTodasMesas() {
        $db = Conexion::conectar();
        $sql = "UPDATE mesa SET activa = 0, estado = 'Disponible', id_cliente = NULL";
        return $db->exec($sql) !== false;
    }
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
    public function actualizarEstado($id, $estado, $idCliente = null) {
        $db = Conexion::conectar();
        $sql = "UPDATE mesa SET estado = ?, id_cliente = ? WHERE id_mesa = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$estado, $idCliente, $id]);
    }
    public function liberarMesa($id) {
        return $this->actualizarEstado($id, 'Disponible', null);
    }
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
