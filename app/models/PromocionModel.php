<?php
require_once 'Conexion.php';
class PromocionModel {
    public function getAll() {
        $db = Conexion::conectar();
        $query = $db->query("SELECT * FROM promocion ORDER BY id_promocion DESC");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
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
    public function getById($id) {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM promocion WHERE id_promocion = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado) {
        $db = Conexion::conectar();
        $sql = "UPDATE promocion SET nombre=?, descripcion=?, fecha_inicio=?, fecha_fin=?, estado=? WHERE id_promocion=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado, $id]);
    }
    public function delete($id) {
        $db = Conexion::conectar();
        $sql = "DELETE FROM promocion WHERE id_promocion=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
    public function getProductosByPromocionId($id_promocion) {
        $db = Conexion::conectar();
        try {
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;
            if (!$tableExists) {
                return [];
            }
            $sql = "SELECT id_producto FROM promocion_producto WHERE id_promocion = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_promocion]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Error en getProductosByPromocionId: " . $e->getMessage());
            return [];
        }
    }
    public function setProductosToPromocion($id_promocion, $productos) {
        $db = Conexion::conectar();
        try {
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;
            if (!$tableExists) {
                $createTable = "CREATE TABLE IF NOT EXISTS promocion_producto (
                    id_promocion INT NOT NULL,
                    id_producto INT NOT NULL,
                    PRIMARY KEY (id_promocion, id_producto),
                    FOREIGN KEY (id_promocion) REFERENCES promocion(id_promocion) ON DELETE CASCADE,
                    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) ON DELETE CASCADE
                )";
                $db->exec($createTable);
            }
            $sqlDelete = "DELETE FROM promocion_producto WHERE id_promocion = ?";
            $stmtDelete = $db->prepare($sqlDelete);
            $stmtDelete->execute([$id_promocion]);
            if (!empty($productos)) {
                $sqlInsert = "INSERT INTO promocion_producto (id_promocion, id_producto) VALUES (?, ?)";
                $stmtInsert = $db->prepare($sqlInsert);
                foreach ($productos as $id_producto) {
                    $stmtInsert->execute([$id_promocion, $id_producto]);
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error en setProductosToPromocion: " . $e->getMessage());
            return true;
        }
    }
    public function getPromocionesConProductos() {
        $db = Conexion::conectar();
        try {
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;
            if (!$tableExists) {
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
            return $this->getAll();
        }
    }
}
?>
