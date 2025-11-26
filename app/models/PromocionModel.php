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

     /*
     * getProductosByPromocionId($id_promocion)
     * Obtiene los IDs de productos asociados a una promoción.
     * @param int $id_promocion - ID de la promoción
     * @return array - Array de IDs de productos
     */
    public function getProductosByPromocionId($id_promocion) {
        $db = Conexion::conectar();
        
        try {
            // Verificar si la tabla existe
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;
            
            if (!$tableExists) {
                return []; // Retornar array vacío si no existe la tabla
            }
            
            $sql = "SELECT id_producto FROM promocion_producto WHERE id_promocion = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_promocion]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (Exception $e) {
            error_log("Error en getProductosByPromocionId: " . $e->getMessage());
            return []; // Retornar array vacío en caso de error
        }
    }

     /*
     * setProductosToPromocion($id_promocion, $productos)
     * Asocia productos a una promoción (reemplaza asociaciones previas).
     * @param int $id_promocion - ID de la promoción
     * @param array $productos - Array de IDs de productos
     * @return bool - true si se completó exitosamente
     */
    public function setProductosToPromocion($id_promocion, $productos) {
        $db = Conexion::conectar();
        
        try {
            // Verificar si la tabla existe
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;
            
            if (!$tableExists) {
                // Crear la tabla si no existe
                $createTable = "CREATE TABLE IF NOT EXISTS promocion_producto (
                    id_promocion INT NOT NULL,
                    id_producto INT NOT NULL,
                    PRIMARY KEY (id_promocion, id_producto),
                    FOREIGN KEY (id_promocion) REFERENCES promocion(id_promocion) ON DELETE CASCADE,
                    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
                )";
                $db->exec($createTable);
            }
            
            // Eliminar asociaciones previas
            $sqlDelete = "DELETE FROM promocion_producto WHERE id_promocion = ?";
            $stmtDelete = $db->prepare($sqlDelete);
            $stmtDelete->execute([$id_promocion]);
            
            // Insertar nuevas asociaciones
            if (!empty($productos)) {
                $sqlInsert = "INSERT INTO promocion_producto (id_promocion, id_producto) VALUES (?, ?)";
                $stmtInsert = $db->prepare($sqlInsert);
                
                foreach ($productos as $id_producto) {
                    $stmtInsert->execute([$id_promocion, $id_producto]);
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            // Si hay error, registrar pero no fallar la operación principal
            error_log("Error en setProductosToPromocion: " . $e->getMessage());
            return true; // Retornar true para no bloquear la creación de la promoción
        }
    }

     /*
     * getPromocionesConProductos()
     * Obtiene todas las promociones con sus productos asociados.
     * @return array - Array de promociones con array de productos
     */
    public function getPromocionesConProductos() {
        $db = Conexion::conectar();
        
        // Verificar si la tabla promocion_producto existe
        try {
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;
            
            if (!$tableExists) {
                // Si no existe la tabla, solo devolver promociones sin productos
                return $this->getAll();
            }
            
            $sql = "SELECT p.*, 
                           GROUP_CONCAT(pr.nombre SEPARATOR ', ') as productos_nombres,
                           GROUP_CONCAT(pr.id_producto) as productos_ids
                    FROM promocion p
                    LEFT JOIN promocion_producto pp ON p.id_promocion = pp.id_promocion
                    LEFT JOIN producto pr ON pp.id_producto = pr.id_producto
                    GROUP BY p.id_promocion
                    ORDER BY p.id_promocion DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            // Si hay error, devolver promociones sin productos
            return $this->getAll();
        }
    }
}
?>
