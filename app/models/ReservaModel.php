<?php
require_once 'Conexion.php';
require_once 'MesaModel.php';
class ReservaModel {
    public function getAll() {
        $db = Conexion::conectar();
        $sql = "SELECT r.*, CONCAT(c.nombre, ' ') as nombre
                FROM reserva r
                LEFT JOIN cliente c ON r.id_cliente = c.id_cliente
                ORDER BY r.id_reserva DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPending() {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE estado = 'pendiente' ORDER BY fecha, hora");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByDate($fecha) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE fecha = ? ORDER BY hora");
        $stmt->execute([$fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM reserva WHERE id_reserva = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function confirm($id) {
        $db = Conexion::conectar();
        try {
            $db->beginTransaction();
            $reserva = $this->getById($id);
            if (!$reserva) {
                $db->rollBack();
                return false;
            }
            if (func_num_args() >= 2) {
                $idMesa = func_get_arg(1);
                $stmt = $db->prepare("UPDATE reserva SET estado = 'confirmada', id_mesa = ? WHERE id_reserva = ?");
                $stmt->execute([$idMesa, $id]);
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
    public function delete($id) {
        $db = Conexion::conectar();
        try {
            $db->beginTransaction();
            $reserva = $this->getById($id);
            if ($reserva && $reserva['id_mesa']) {
                $mesaModel = new MesaModel();
                $mesaModel->liberarMesa($reserva['id_mesa']);
            }
            $stmt = $db->prepare("DELETE FROM reserva WHERE id_reserva = ?");
            $stmt->execute([$id]);
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
    public function getMesasActivasYDisponibles($fecha) {
        $db = Conexion::conectar();
        try {
            error_log("[getMesasActivasYDisponibles] Consultando disponibilidad para fecha: $fecha");
            require_once 'DisponibilidadModel.php';
            $dispModel = new DisponibilidadModel();
            $disponibilidad = $dispModel->getByDate($fecha);
            if (!$disponibilidad || !isset($disponibilidad['cantidad'])) {
                error_log("[getMesasActivasYDisponibles] No hay disponibilidad configurada para $fecha");
                return [];
            }
            $cantidadPermitida = (int)$disponibilidad['cantidad'];
            error_log("[getMesasActivasYDisponibles] Cantidad permitida: $cantidadPermitida");
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
                return [];
            }
            $sql = "SELECT id_mesa 
                    FROM reserva 
                    WHERE fecha = ? 
                    AND estado IN ('pendiente', 'confirmada') 
                    AND id_mesa IS NOT NULL";
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha]);
            $mesasReservadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("[getMesasActivasYDisponibles] Mesas ya reservadas: " . count($mesasReservadas));
            $mesasDisponibles = array_filter($mesasActivas, function($mesa) use ($mesasReservadas) {
                return !in_array($mesa['id_mesa'], $mesasReservadas);
            });
            $resultado = array_values($mesasDisponibles);
            error_log("[getMesasActivasYDisponibles] Mesas disponibles finales: " . count($resultado));
            return $resultado;
        } catch (Exception $e) {
            error_log("[getMesasActivasYDisponibles] Error: " . $e->getMessage());
            throw $e;
        }
    }
    public function getReservasPorFechaConMesas($fecha) {
        $db = Conexion::conectar();
        try {
            require_once 'DisponibilidadModel.php';
            $dispModel = new DisponibilidadModel();
            $disponibilidad = $dispModel->getByDate($fecha);
            if (!$disponibilidad || !isset($disponibilidad['cantidad'])) {
                return ['disponibilidad' => false, 'mesas' => []];
            }
            $cantidadPermitida = (int)$disponibilidad['cantidad'];
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
            $sql = "SELECT r.*, c.nombre as cliente_nombre
                    FROM reserva r
                    LEFT JOIN cliente c ON r.id_cliente = c.id_cliente
                    WHERE r.fecha = ? 
                    AND r.id_mesa IS NOT NULL";
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha]);
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $reservasPorMesa = [];
            foreach ($reservas as $reserva) {
                $reservasPorMesa[$reserva['id_mesa']] = $reserva;
            }
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
