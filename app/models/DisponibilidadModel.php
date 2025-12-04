<?php

require_once 'Conexion.php';
require_once 'MesaModel.php';

/**
 * Modelo de Disponibilidad
 * Gestiona la disponibilidad de mesas por fecha
 */
class DisponibilidadModel
{
    /**
     * Obtiene todas las disponibilidades registradas.
     * Retorna ordenadas por fecha descendente.
     * 
     * @return array Array de disponibilidades
     */
    public function getAll()
    {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM mesas_disponibilidad ORDER BY fecha DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la disponibilidad para una fecha específica.
     * 
     * @param string $fecha Fecha en formato Y-m-d
     * @return array|false Registro de disponibilidad o false si no existe
     */
    public function getByDate($fecha)
    {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM mesas_disponibilidad WHERE fecha = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea o actualiza disponibilidad para una fecha.
     * Si existe, actualiza la cantidad. Si no, crea nuevo registro.
     * Utiliza transacción para garantizar consistencia.
     * 
     * @param string $fecha Fecha en formato Y-m-d
     * @param int $cantidad Cantidad de mesas disponibles
     * @return bool True si se guardó exitosamente
     * @throws Exception Si hay error en la transacción
     */
    public function create($fecha, $cantidad)
    {
        $db = Conexion::conectar();

        try {
            $db->beginTransaction();

            // Verificar si ya existe disponibilidad para esa fecha
            $sql = "SELECT * FROM mesas_disponibilidad WHERE fecha = ? LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Actualizar disponibilidad existente
                error_log("Actualizando disponibilidad existente para fecha: $fecha");
                $sql = "UPDATE mesas_disponibilidad SET cantidad = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$cantidad, $existing['id']]);
            } else {
                // Crear nueva disponibilidad
                error_log("Creando nueva disponibilidad para fecha: $fecha");
                $sql = "INSERT INTO mesas_disponibilidad (fecha, cantidad) VALUES (?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$fecha, $cantidad]);
            }

            // Activar mesas según cantidad
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

    /**
     * Actualiza la cantidad de mesas disponibles para un registro existente.
     * Utiliza transacción para garantizar consistencia.
     * 
     * @param int $id ID del registro de disponibilidad
     * @param int $cantidad Nueva cantidad de mesas
     * @return bool True si se actualizó exitosamente
     */
    public function update($id, $cantidad)
    {
        $db = Conexion::conectar();

        try {
            $db->beginTransaction();

            // Actualizar cantidad
            $sql = "UPDATE mesas_disponibilidad SET cantidad = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$cantidad, $id]);

            // Activar mesas según cantidad
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
     * Verifica si una fecha tiene reservas activas (pendientes o confirmadas).
     * 
     * @param string $fecha Fecha en formato Y-m-d
     * @return bool True si hay reservas activas en esa fecha
     */
    public function tieneReservas($fecha)
    {
        $db = Conexion::conectar();
        $sql = "SELECT COUNT(*) as total FROM reserva 
                WHERE fecha = ? AND estado IN ('pendiente', 'confirmada')";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fecha]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    /**
     * Elimina un registro de disponibilidad por su ID.
     * 
     * @param int $id ID del registro de disponibilidad
     * @return bool True si se eliminó exitosamente
     */
    public function delete($id)
    {
        $db = Conexion::conectar();
        $sql = "DELETE FROM mesas_disponibilidad WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Obtiene todas las disponibilidades con información de reservas.
     * Incluye bandera indicando si existen reservas activas para cada fecha.
     * 
     * @return array Array de disponibilidades con información de reservas
     */
    public function getAllWithReservationCheck()
    {
        $db = Conexion::conectar();
        $sql = "SELECT d.*, 
                       (SELECT COUNT(*) FROM reserva r 
                        WHERE r.fecha = d.fecha 
                        AND r.estado IN ('pendiente', 'confirmada')) as tiene_reservas
                FROM mesas_disponibilidad d
                ORDER BY d.fecha DESC";
        $stmt = $db->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir contador a booleano
        foreach ($results as &$row) {
            $row['tiene_reservas'] = (int)$row['tiene_reservas'] > 0;
        }

        return $results;
    }
}
?>