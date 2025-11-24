<?php
 /*
 * Evento.php
 * 
 * Modelo para gestionar eventos con soporte opcional para imágenes.
 * Maneja CRUD: create, read, update, delete.
 * 
 * Tabla: evento (id_evento, nombre, descripcion, fecha, hora_inicio, hora_fin)
 * 
 * Métodos:
 * - getAll(): Retorna todos los eventos
 * - create($nombre, $descripcion, $fecha, $hora_inicio, $hora_fin)
 * - getById($id): Obtiene un evento por ID
 * - update($id, $nombre, $descripcion, $fecha, $hora_inicio, $hora_fin)
 * - delete($id): Elimina un evento
 */

require_once 'Conexion.php';

class EventoModel {

     /*
     * getAll()
     * Retorna todos los eventos ordenados DESC por ID.
     * @return array - Array de eventos
     */
    public function getAll() {
        $db = Conexion::conectar();
        $query = $db->query("SELECT * FROM evento ORDER BY id_evento DESC");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

     /*
    * create($nombre, $descripcion, $fecha, $hora_inicio, $hora_fin)
     * 
    * Crea nuevo evento. (La imagen se gestiona por filesystem, no en BD).
     * 
     * @param string $nombre - Nombre del evento
     * @param string $descripcion - Descripción
     * @param string $fecha - Fecha (YYYY-MM-DD)
     * @param string $hora_inicio - Hora inicio (HH:MM)
     * @param string $hora_fin - Hora fin (HH:MM)
     * @return int|false - ID insertado si se insertó, false si hubo error
     */
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

     /*
     * getById($id)
     * Obtiene un evento por ID.
     * @param int $id - ID del evento
     * @return array|false - Datos del evento o false
     */
    public function getById($id) {
        $db = Conexion::conectar();
        $sql = "SELECT * FROM evento WHERE id_evento = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     /*
    * update($id, $nombre, $descripcion, $fecha, $hora_inicio, $hora_fin)
     * 
    * Actualiza un evento. (La imagen se gestiona por filesystem, no en BD).
     * 
     * @param int $id - ID del evento
     * @param string $nombre - Nuevo nombre
     * @param string $descripcion - Nueva descripción
     * @param string $fecha - Nueva fecha
     * @param string $hora_inicio - Nueva hora inicio
     * @param string $hora_fin - Nueva hora fin
     * @return bool - true si se actualizó, false si hubo error
     */
    public function update($id, $nombre, $descripcion, $fecha, $hora_inicio, $hora_fin) {
        $db = Conexion::conectar();
        $sql = "UPDATE evento SET nombre=?, descripcion=?, fecha=?, hora_inicio=?, hora_fin=? WHERE id_evento=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $fecha, $hora_inicio, $hora_fin, $id]);
    }

     /*
     * delete($id)
     * Elimina un evento.
     * @param int $id - ID del evento
     * @return bool - true si se eliminó, false si hubo error
     */
    public function delete($id) {
        $db = Conexion::conectar();
        $sql = "DELETE FROM evento WHERE id_evento=?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>