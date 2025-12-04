<?php

// Incluir la clase de conexión a base de datos
require_once 'Conexion.php';

/**
 * ProductoModel
 * Clase encargada de gestionar los productos del sistema
 * Proporciona métodos para crear, leer, actualizar y eliminar productos en la base de datos
 */
class ProductoModel
{
    /**
     * Obtiene todos los productos registrados en la base de datos
     * Devuelve los productos ordenados por ID en orden descendente
     *
     * @return array Array asociativo con todos los productos o array vacío si no hay registros
     */
    public function getAll()
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Ejecutar consulta para obtener todos los productos
        $query = $db->query("SELECT * FROM producto ORDER BY id_producto DESC");

        // Retornar resultados como array asociativo
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo producto en la base de datos
     * Inserta los datos del producto con nombre, precio y categoría
     *
     * @param string $nombre Nombre del producto
     * @param float $precio Precio del producto (debe ser numérico y positivo)
     * @param string $categoria Categoría del producto
     * @return bool true si la inserción fue exitosa, false en caso contrario
     */
    public function create($nombre, $precio, $categoria)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de inserción
        $sql = "INSERT INTO producto (nombre, precio, categoria) "
            . "VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta con los parámetros y retornar resultado
        return $stmt->execute([$nombre, $precio, $categoria]);
    }

    /**
     * Obtiene un producto específico por su ID
     * Busca un producto en la base de datos usando el ID proporcionado
     *
     * @param int $id ID del producto a buscar
     * @return array|false Array asociativo con los datos del producto o false si no existe
     */
    public function getById($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de búsqueda por ID
        $sql = "SELECT * FROM producto WHERE id_producto = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta con el ID del producto
        $stmt->execute([$id]);

        // Retornar el producto encontrado o false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos de un producto existente
     * Modifica el registro del producto con los nuevos valores proporcionados
     *
     * @param int $id ID del producto a actualizar
     * @param string $nombre Nuevo nombre del producto
     * @param float $precio Nuevo precio del producto
     * @param string $categoria Nueva categoría del producto
     * @return bool true si la actualización fue exitosa, false en caso contrario
     */
    public function update($id, $nombre, $precio, $categoria)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de actualización
        $sql = "UPDATE producto SET nombre = ?, precio = ?, categoria = ? "
            . "WHERE id_producto = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta y retornar resultado
        return $stmt->execute([$nombre, $precio, $categoria, $id]);
    }

    /**
     * Elimina un producto de la base de datos
     * Borra el registro del producto especificado por su ID
     *
     * @param int $id ID del producto a eliminar
     * @return bool true si la eliminación fue exitosa, false en caso contrario
     */
    public function delete($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de eliminación
        $sql = "DELETE FROM producto WHERE id_producto = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta y retornar resultado
        return $stmt->execute([$id]);
    }
}

?>