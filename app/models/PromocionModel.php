<?php
 /*
 * PromocionModel.php
 * 
 * Modelo para gestionar promociones con soporte opcional para imágenes.
 * Maneja CRUD: create, read, update, delete.
 * 
 * Tabla: promocion (id_promocion, nombre, descripcion, fecha_inicio, fecha_fin, estado, imagen)
 * 
 * Métodos:
 * - getAll(): Retorna todas las promociones
 * - create(..., $imagen): Crea nueva promoción (imagen es parámetro variádico opcional)
 * - getById($id): Obtiene una promoción por ID
 * - update(..., $imagen): Actualiza promoción (imagen opcional)
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
     * create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado[, $imagen])
     * 
     * Crea nueva promoción. La imagen es parámetro variádico (6to parámetro, OPCIONAL).
     * 
     * @param string $nombre - Nombre de la promoción
     * @param string $descripcion - Descripción
     * @param string $fecha_inicio - Fecha inicio (YYYY-MM-DD)
     * @param string $fecha_fin - Fecha fin (YYYY-MM-DD)
     * @param string $estado - 'activo' o 'inactivo'
     * @param string|null $imagen - (Opcional) Nombre del archivo de imagen
     * @return bool - true si se insertó, false si hubo error
     */
    public function create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado) {
        $db = Conexion::conectar();
        $args = [$nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado];
        if (func_num_args() >= 6) {
            $imagen = func_get_arg(5);
            $sql = "INSERT INTO promocion (nombre, descripcion, fecha_inicio, fecha_fin, estado, imagen) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $args[] = $imagen;
        } else {
            $sql = "INSERT INTO promocion (nombre, descripcion, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
        }
        return $stmt->execute($args);
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
     * update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado[, $imagen])
     * 
     * Actualiza una promoción. La imagen es parámetro variádico (7mo parámetro opcional).
     * 
     * @param int $id - ID de la promoción
     * @param string $nombre - Nuevo nombre
     * @param string $descripcion - Nueva descripción
     * @param string $fecha_inicio - Nueva fecha inicio
     * @param string $fecha_fin - Nueva fecha fin
     * @param string $estado - Nuevo estado
     * @param string|null $imagen - (Opcional) Nuevo nombre de imagen
     * @return bool - true si se actualizó, false si hubo error
     */
    public function update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado) {
        $db = Conexion::conectar();
        if (func_num_args() >= 7) {
            $imagen = func_get_arg(6);
            if ($imagen) {
                $sql = "UPDATE promocion SET nombre=?, descripcion=?, fecha_inicio=?, fecha_fin=?, estado=?, imagen=? WHERE id_promocion=?";
                $stmt = $db->prepare($sql);
                return $stmt->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado, $imagen, $id]);
            }
        }
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
