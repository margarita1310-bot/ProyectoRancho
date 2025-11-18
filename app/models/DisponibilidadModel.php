<?php
/**
 * Disponibilidad.php
 * 
 * Modelo para gestionar disponibilidad de mesas por fecha.
 * Permite crear, actualizar y consultar registros de mesas disponibles.
 * 
 * Tabla: mesas_disponibilidad (id, fecha, cantidad, created_at)
 * 
 * Métodos:
 * - getByDate($fecha): Obtiene disponibilidad para una fecha
 * - create($fecha, $cantidad): Crea disponibilidad o actualiza si existe (UPSERT)
 * - update($id, $cantidad): Actualiza cantidad para un registro
 * - delete($id): Elimina un registro
 */

require_once 'Conexion.php';

class DisponibilidadModel {
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
     * 
     * Lógica:
     * 1. Busca si ya existe registro para esa fecha
     * 2. Si existe: UPDATE la cantidad
     * 3. Si no existe: INSERT nuevo registro
     * 
     * @param string $fecha - Fecha en formato YYYY-MM-DD
     * @param int $cantidad - Cantidad de mesas disponibles
     * @return bool - true si se insertó/actualizó, false si hubo error
     */
    public function create($fecha, $cantidad) {
        $db = Conexion::conectar();
        // Si ya existe, actualizar cantidad
        $existing = $this->getByDate($fecha);
        if ($existing) {
            $sql = "UPDATE mesas_disponibilidad SET cantidad = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            return $stmt->execute([$cantidad, $existing['id']]);
        }
        $sql = "INSERT INTO mesas_disponibilidad (fecha, cantidad) VALUES (?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$fecha, $cantidad]);
    }

    /**
     * update($id, $cantidad)
     * 
     * Actualiza la cantidad de mesas para un registro existente.
     * 
     * @param int $id - ID del registro
     * @param int $cantidad - Nueva cantidad
     * @return bool - true si se actualizó, false si hubo error
     */
    public function update($id, $cantidad) {
        $db = Conexion::conectar();
        $sql = "UPDATE mesas_disponibilidad SET cantidad = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$cantidad, $id]);
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
}
?>