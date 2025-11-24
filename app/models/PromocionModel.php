<?php
 /*
 * PromocionModel.php
 * 
 * Modelo para gestionar promociones con soporte opcional para imágenes.
 * Maneja CRUD: create, read, update, delete.
 * 
 * Tabla: promocion (id_promocion, nombre, descripcion, fecha_inicio, fecha_fin, estado)
 * 
 * Métodos:
 * - getAll(): Retorna todas las promociones
 * - create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)
 * - getById($id): Obtiene una promoción por ID
 * - update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)
 * - delete($id): Elimina una promoción
 */

require_once 'Conexion.php';

class PromocionModel {

     /*
     * getAll()
     * Retorna todas las promociones ordenadas DESC por ID.
     * @return array - Array de promociones
     */
    public function getAll() {
        $db = Conexion::conectar();
        $query = $db->query("SELECT * FROM promocion ORDER BY id_promocion DESC");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

     /*
    * create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)
     * 
    * Crea nueva promoción. (La imagen se gestiona por filesystem, no en BD).
     * 
     * @param string $nombre - Nombre de la promoción
     * @param string $descripcion - Descripción
     * @param string $fecha_inicio - Fecha inicio (YYYY-MM-DD)
     * @param string $fecha_fin - Fecha fin (YYYY-MM-DD)
     * @param string $estado - 'activo' o 'inactivo'
     * @return int|false - ID insertado si se insertó, false si hubo error
     */
    public function create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado) {
        $db = Conexion::conectar();
        $sql = "INSERT INTO promocion (nombre, descripcion, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado]);
        if ($ok) {
            return (int)$db->lastInsertId();
        }
        return false;
    }

     /*
     * getById($id)
     * Obtiene una promoción por ID.
     * @param int $id - ID de la promoción
     * @return array|false - Datos de la promoción o false
     */
    public function getById($id) {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM promocion WHERE id_promocion = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     /*
    * update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)
     * 
    * Actualiza una promoción. (La imagen se gestiona por filesystem, no en BD).
     * 
     * @param int $id - ID de la promoción
     * @param string $nombre - Nuevo nombre
     * @param string $descripcion - Nueva descripción
     * @param string $fecha_inicio - Nueva fecha inicio
     * @param string $fecha_fin - Nueva fecha fin
     * @param string $estado - Nuevo estado
     * @return bool - true si se actualizó, false si hubo error
     */
    public function update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado) {
        $db = Conexion::conectar();
        $sql = "UPDATE promocion SET nombre=?, descripcion=?, fecha_inicio=?, fecha_fin=?, estado=? WHERE id_promocion=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado, $id]);
    }

     /*
     * delete($id)
     * Elimina una promoción.
     * @param int $id - ID de la promoción
     * @return bool - true si se eliminó, false si hubo error
     */
    public function delete($id) {
        $db = Conexion::conectar();
        $sql = "DELETE FROM promocion WHERE id_promocion=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
