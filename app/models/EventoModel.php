<?php
require_once 'Conexion.php';
class EventoModel {
    public function getAll() {
        $db = Conexion::conectar();
        $query = $db->query("SELECT * FROM evento ORDER BY id_evento DESC");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($nombre, $descripcion, $fecha, $hora_inicio, $hora_fin) {
        $db = Conexion::conectar();
        $sql = "INSERT INTO evento (nombre, descripcion, fecha, hora_inicio, hora_fin) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([$nombre, $descripcion, $fecha, $hora_inicio, $hora_fin]);
        if ($ok) {
            return (int)$db->lastInsertId();
        }
        return false;
    }
    public function getById($id) {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM evento WHERE id_evento = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update($id, $nombre, $descripcion, $fecha, $hora_inicio, $hora_fin) {
        $db = Conexion::conectar();
        $sql = "UPDATE evento SET nombre=?, descripcion=?, fecha=?, hora_inicio=?, hora_fin=? WHERE id_evento=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $fecha, $hora_inicio, $hora_fin, $id]);
    }
    public function delete($id) {
        $db = Conexion::conectar();
        $sql = "DELETE FROM evento WHERE id_evento=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>