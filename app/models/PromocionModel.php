<?php

// Incluir la clase de conexión a base de datos
require_once 'Conexion.php';

/**
 * PromocionModel
 * Clase encargada de gestionar las promociones del sistema
 * Proporciona métodos para crear, leer, actualizar, eliminar promociones y gestionar productos asociados
 */
class PromocionModel
{
    /**
     * Obtiene todas las promociones registradas en la base de datos
     * Devuelve las promociones ordenadas por ID en orden descendente
     *
     * @return array Array asociativo con todas las promociones o array vacío si no hay registros
     */
    public function getAll()
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Ejecutar consulta para obtener todas las promociones
        $query = $db->query("SELECT * FROM promocion ORDER BY id_promocion DESC");

        // Retornar resultados como array asociativo
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva promoción en la base de datos
     * Inserta los datos de la promoción y retorna el ID del registro creado
     *
     * @param string $nombre Nombre de la promoción
     * @param string $descripcion Descripción de la promoción
     * @param string $fecha_inicio Fecha de inicio de la promoción (formato: YYYY-MM-DD)
     * @param string $fecha_fin Fecha de finalización de la promoción (formato: YYYY-MM-DD)
     * @param string $estado Estado de la promoción (activa/inactiva)
     * @return int|false ID de la promoción creada o false si falla la inserción
     */
    public function create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de inserción
        $sql = "INSERT INTO promocion (nombre, descripcion, fecha_inicio, fecha_fin, estado) "
            . "VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta con los parámetros
        $ok = $stmt->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado]);

        // Si la inserción fue exitosa, retornar el ID de la nueva promoción
        if ($ok) {
            return (int)$db->lastInsertId();
        }

        // Si falla, retornar false
        return false;
    }

    /**
     * Obtiene una promoción específica por su ID
     * Busca una promoción en la base de datos usando el ID proporcionado
     *
     * @param int $id ID de la promoción a buscar
     * @return array|false Array asociativo con los datos de la promoción o false si no existe
     */
    public function getById($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de búsqueda por ID
        $sql = "SELECT * FROM promocion WHERE id_promocion = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta con el ID de la promoción
        $stmt->execute([$id]);

        // Retornar la promoción encontrada o false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos de una promoción existente
     * Modifica el registro de la promoción con los nuevos valores proporcionados
     *
     * @param int $id ID de la promoción a actualizar
     * @param string $nombre Nuevo nombre de la promoción
     * @param string $descripcion Nueva descripción de la promoción
     * @param string $fecha_inicio Nueva fecha de inicio (formato: YYYY-MM-DD)
     * @param string $fecha_fin Nueva fecha de finalización (formato: YYYY-MM-DD)
     * @param string $estado Nuevo estado de la promoción
     * @return bool true si la actualización fue exitosa, false en caso contrario
     */
    public function update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de actualización
        $sql = "UPDATE promocion SET nombre = ?, descripcion = ?, fecha_inicio = ?, "
            . "fecha_fin = ?, estado = ? WHERE id_promocion = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta y retornar resultado
        return $stmt->execute([$nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado, $id]);
    }

    /**
     * Elimina una promoción de la base de datos
     * Borra el registro de la promoción especificado por su ID
     *
     * @param int $id ID de la promoción a eliminar
     * @return bool true si la eliminación fue exitosa, false en caso contrario
     */
    public function delete($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de eliminación
        $sql = "DELETE FROM promocion WHERE id_promocion = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta y retornar resultado
        return $stmt->execute([$id]);
    }

    /**
     * Obtiene los IDs de productos asociados a una promoción
     * Consulta la tabla relacional promocion_producto para obtener los IDs
     *
     * @param int $id_promocion ID de la promoción
     * @return array Array de IDs de productos o array vacío si no hay asociaciones
     */
    public function getProductosByPromocionId($id_promocion)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        try {
            // Verificar si la tabla de relación existe
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;

            // Si la tabla no existe, retornar array vacío
            if (!$tableExists) {
                return [];
            }

            // Consultar los IDs de productos de la promoción
            $sql = "SELECT id_producto FROM promocion_producto WHERE id_promocion = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_promocion]);

            // Retornar los resultados como array de una columna
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            // Si ocurre un error, registrarlo y retornar array vacío
            error_log("Error en getProductosByPromocionId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Asigna productos a una promoción
     * Crea o actualiza la asociación de productos con una promoción en la tabla relacional
     *
     * @param int $id_promocion ID de la promoción
     * @param array $productos Array de IDs de productos a asociar
     * @return bool true si la operación fue exitosa (o si la tabla no existe)
     */
    public function setProductosToPromocion($id_promocion, $productos)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        try {
            // Verificar si la tabla de relación existe
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;

            // Si la tabla no existe, crearla
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

            // Eliminar todas las asociaciones previas de productos para esta promoción
            $sqlDelete = "DELETE FROM promocion_producto WHERE id_promocion = ?";
            $stmtDelete = $db->prepare($sqlDelete);
            $stmtDelete->execute([$id_promocion]);

            // Insertar las nuevas asociaciones de productos
            if (!empty($productos)) {
                $sqlInsert = "INSERT INTO promocion_producto (id_promocion, id_producto) VALUES (?, ?)";
                $stmtInsert = $db->prepare($sqlInsert);

                // Iterar sobre los productos e insertar cada uno
                foreach ($productos as $id_producto) {
                    $stmtInsert->execute([$id_promocion, $id_producto]);
                }
            }

            // Retornar true si todo fue exitoso
            return true;
        } catch (Exception $e) {
            // Si ocurre un error, registrarlo pero retornar true para no interrumpir el flujo
            error_log("Error en setProductosToPromocion: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Obtiene todas las promociones con información de productos asociados
     * Retorna promociones con nombres e IDs de productos agrupados
     *
     * @return array Array de arrays asociativos con promociones y productos, o solo promociones si la tabla no existe
     */
    public function getPromocionesConProductos()
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        try {
            // Verificar si la tabla de relación existe
            $checkTable = $db->query("SHOW TABLES LIKE 'promocion_producto'");
            $tableExists = $checkTable->rowCount() > 0;

            // Si la tabla no existe, retornar solo las promociones
            if (!$tableExists) {
                return $this->getAll();
            }

            // Consulta con JOIN para obtener promociones con productos asociados
            $sql = "SELECT p.*, "
                . "GROUP_CONCAT(pr.nombre SEPARATOR ', ') as productos_nombres, "
                . "GROUP_CONCAT(pr.id_producto) as productos_ids "
                . "FROM promocion p "
                . "LEFT JOIN promocion_producto pp ON p.id_promocion = pp.id_promocion "
                . "LEFT JOIN producto pr ON pp.id_producto = pr.id_producto "
                . "GROUP BY p.id_promocion "
                . "ORDER BY p.id_promocion DESC";

            // Ejecutar la consulta
            $stmt = $db->query($sql);

            // Retornar resultados agrupados
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Si ocurre un error, retornar solo las promociones sin productos
            return $this->getAll();
        }
    }
}

?>
