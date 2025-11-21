<?php
/**
 * Disponibilidad.php
 * 
 * Modelo para gestionar disponibilidad de mesas por fecha.
 * Permite crear, actualizar y consultar registros de mesas disponibles.
 * Al crear disponibilidad, activa las mesas correspondientes en la tabla mesa.
 * 
 * Tabla: mesas_disponibilidad (id, fecha, cantidad, created_at)
 * 
 * Métodos:
 * - getByDate($fecha): Obtiene disponibilidad para una fecha
 * - create($fecha, $cantidad): Crea disponibilidad o actualiza si existe (UPSERT) y activa mesas
 * - update($id, $cantidad): Actualiza cantidad para un registro y activa mesas
 * - delete($id): Elimina un registro
 * - tieneReservas($fecha): Verifica si hay reservas activas para una fecha
 */

require_once 'Conexion.php';
require_once 'MesaModel.php';

class DisponibilidadModel {
    /**
     * getAll()
     * 
     * Obtiene todos los registros de disponibilidad ordenados por fecha DESC.
     * 
     * @return array - Array de registros de disponibilidad
     */
    public function getAll() {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM mesas_disponibilidad ORDER BY fecha DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * getByDate($fecha)
     * 
     * Busca el registro de disponibilidad para una fecha específica.
     * 
     * @param string $fecha - Fecha en formato YYYY-MM-DD
     * @return array|false - Registro de disponibilidad o false si no existe
     */
    public function getByDate($fecha) {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM mesas_disponibilidad WHERE fecha = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * create($fecha, $cantidad)
     * 
     * Crea un registro de disponibilidad para una fecha.
     * Si ya existe disponibilidad para esa fecha, actualiza la cantidad (UPSERT).
     * Además, activa las mesas correspondientes en la tabla mesa.
     * 
     * Lógica:
     * 1. Busca si ya existe registro para esa fecha
     * 2. Si existe: UPDATE la cantidad
     * 3. Si no existe: INSERT nuevo registro
     * 4. Activa las mesas del 1 hasta $cantidad
     * 
     * @param string $fecha - Fecha en formato YYYY-MM-DD
     * @param int $cantidad - Cantidad de mesas disponibles
     * @return bool - true si se insertó/actualizó, false si hubo error
     */
    public function create($fecha, $cantidad) {
        $db = Conexion::conectar();
        
        try {
            $db->beginTransaction();
            
            // Buscar si ya existe registro para esa fecha (usando la misma conexión)
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
            
            // Activar mesas correspondientes
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
            throw $e; // Re-lanzar la excepción para que sea capturada en el controlador
        }
    }

    /**
     * update($id, $cantidad)
     * 
     * Actualiza la cantidad de mesas para un registro existente.
     * También actualiza las mesas activas.
     * 
     * @param int $id - ID del registro
     * @param int $cantidad - Nueva cantidad
     * @return bool - true si se actualizó, false si hubo error
     */
    public function update($id, $cantidad) {
        $db = Conexion::conectar();
        
        try {
            $db->beginTransaction();
            
            $sql = "UPDATE mesas_disponibilidad SET cantidad = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$cantidad, $id]);
            
            // Activar mesas correspondientes
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

    /**
     * tieneReservas($fecha)
     * 
     * Verifica si existen reservas activas (pendientes o confirmadas) para una fecha.
     * Se utiliza para validar si se puede modificar la disponibilidad.
     * 
     * @param string $fecha - Fecha en formato YYYY-MM-DD
     * @return bool - true si hay reservas activas, false si no hay
     */
    public function tieneReservas($fecha) {
        $db = Conexion::conectar();
        $sql = "SELECT COUNT(*) as total FROM reserva 
                WHERE fecha = ? AND estado IN ('pendiente', 'confirmada')";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    /**
     * delete($id)
     * 
     * Elimina un registro de disponibilidad.
     * 
     * @param int $id - ID del registro
     * @return bool - true si se eliminó, false si hubo error
     */
    public function delete($id) {
        $db = Conexion::conectar();
        $sql = "DELETE FROM mesas_disponibilidad WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * getAllWithReservationCheck()
     * 
     * Obtiene todas las disponibilidades e incluye un indicador
     * que muestra si cada una tiene reservas asociadas.
     * 
     * @return array - Array de disponibilidades con campo 'tiene_reservas'
     */
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
        
        // Convertir tiene_reservas a booleano
        foreach ($results as &$row) {
            $row['tiene_reservas'] = (int)$row['tiene_reservas'] > 0;
        }
        
        return $results;
    }
}
?>