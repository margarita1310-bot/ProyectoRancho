<?php
 /*
 * ProductoModel.php
 * 
 * Modelo para gestionar productos del menú del restaurante.
 * Maneja CRUD básico: create, read, update, delete.
 * 
 * Tabla: producto (id_producto, nombre, precio, categoria)
 * 
 * Métodos:
 * - getAll(): Retorna todos los productos
 * - create($nombre, $precio, $categoria): Crea nuevo producto
 * - getById($id): Obtiene un producto por ID
 * - update($id, $nombre, $precio, $categoria): Actualiza un producto
 * - delete($id): Elimina un producto
 */

require_once 'Conexion.php';

class ProductoModel {

     /*
     * getAll()
     * Retorna todos los productos ordenados descendentemente por ID.
     * @return array - Array de productos (cada uno es un array asociativo)
     */
    public function getAll() {
        $db = Conexion::conectar();
        $query = $db->query("SELECT * FROM producto ORDER BY id_producto DESC");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

     /*
     * create($nombre, $precio, $categoria)
     * 
     * Crea un nuevo producto en la base de datos.
     * 
     * @param string $nombre - Nombre del producto
     * @param float $precio - Precio del producto (>= 0)
     * @param string $categoria - Categoría del producto
     * @return bool - true si se insertó, false si hubo error
     */
    public function create($nombre, $precio, $categoria) {
        $db = Conexion::conectar();
        $sql = "INSERT INTO producto (nombre, precio, categoria)
                VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $precio, $categoria]);
    }

     /*
     * getById($id)
     * Obtiene un producto específico por su ID.
     * @param int $id - ID del producto
     * @return array|false - Array asociativo con datos del producto o false si no existe
     */
    public function getById($id) {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM producto WHERE id_producto = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     /*
     * update($id, $nombre, $precio, $categoria)
     * 
     * Actualiza un producto existente.
     * 
     * @param int $id - ID del producto
     * @param string $nombre - Nuevo nombre
     * @param float $precio - Nuevo precio
     * @param string $categoria - Nueva categoría
     * @return bool - true si se actualizó, false si hubo error
     */
    public function update($id, $nombre, $precio, $categoria) {
        $db = Conexion::conectar();
        $sql = "UPDATE producto SET nombre=?, precio=?, categoria=?
                WHERE id_producto=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $precio, $categoria, $id]);
    }

     /*
     * delete($id)
     * Elimina un producto de la base de datos.
     * @param int $id - ID del producto
     * @return bool - true si se eliminó, false si hubo error
     */
    public function delete($id) {
        $db = Conexion::conectar();
        $sql = "DELETE FROM producto WHERE id_producto=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>