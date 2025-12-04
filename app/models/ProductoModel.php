<?php
require_once 'Conexion.php';
class ProductoModel {
    public function getAll() {
        $db = Conexion::conectar();
        $query = $db->query("SELECT * FROM producto ORDER BY id_producto DESC");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($nombre, $precio, $categoria) {
        $db = Conexion::conectar();
        $sql = "INSERT INTO producto (nombre, precio, categoria)
                VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $precio, $categoria]);
    }
    public function getById($id) {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM producto WHERE id_producto = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update($id, $nombre, $precio, $categoria) {
        $db = Conexion::conectar();
        $sql = "UPDATE producto SET nombre=?, precio=?, categoria=?
                WHERE id_producto=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $precio, $categoria, $id]);
    }
    public function delete($id) {
        $db = Conexion::conectar();
        $sql = "DELETE FROM producto WHERE id_producto=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>