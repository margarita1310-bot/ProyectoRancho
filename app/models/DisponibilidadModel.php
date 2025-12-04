<?php
require_once 'Conexion.php';
require_once 'MesaModel.php';
class DisponibilidadModel {
    public function getAll() {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM mesas_disponibilidad ORDER BY fecha DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByDate($fecha) {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM mesas_disponibilidad WHERE fecha = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($fecha, $cantidad) {
        $db = Conexion::conectar();
        try {
            $db->beginTransaction();
            $sql = "SELECT * FROM mesas_disponibilidad WHERE fecha = ? LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                error_log("Actualizando disponibilidad existente para fecha: $fecha");
                $sql = "UPDATE mesas_disponibilidad SET cantidad = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$cantidad, $existing['id']]);
            } else {
                error_log("Creando nueva disponibilidad para fecha: $fecha");
                $sql = "INSERT INTO mesas_disponibilidad (fecha, cantidad) VALUES (?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$fecha, $cantidad]);
            }
            error_log("Activando $cantidad mesas");
            $mesaModel = new MesaModel();
            $mesaModel->activarMesas($cantidad);
            $db->commit();
            error_log("Disponibilidad guardada exitosamente");
            return true;
        } catch (Exception $e) {
            error_log("Error en DisponibilidadModel::create - " . $e->getMessage());
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }
    public function update($id, $cantidad) {
        $db = Conexion::conectar();
        try {
            $db->beginTransaction();
            $sql = "UPDATE mesas_disponibilidad SET cantidad = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$cantidad, $id]);
            $mesaModel = new MesaModel();
            $mesaModel->activarMesas($cantidad);
            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }
    public function tieneReservas($fecha) {
        $db = Conexion::conectar();
        $sql = "SELECT COUNT(*) as total FROM reserva 
                WHERE fecha = ? AND estado IN ('pendiente', 'confirmada')";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
    public function delete($id) {
        $db = Conexion::conectar();
        $sql = "DELETE FROM mesas_disponibilidad WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
    public function getAllWithReservationCheck() {
        $db = Conexion::conectar();
        $sql = "SELECT d.*, 
                       (SELECT COUNT(*) FROM reserva r 
                        WHERE r.fecha = d.fecha 
                        AND r.estado IN ('pendiente', 'confirmada')) as tiene_reservas
                FROM mesas_disponibilidad d
                ORDER BY d.fecha DESC";
        $stmt = $db->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$row) {
            $row['tiene_reservas'] = (int)$row['tiene_reservas'] > 0;
        }
        return $results;
    }
}
?>