<?php

// Incluir la clase de conexión a base de datos
require_once 'Conexion.php';

/**
 * EventoModel
 * Clase encargada de gestionar los eventos del sistema
 * Proporciona métodos para crear, leer, actualizar y eliminar eventos en la base de datos
 */
class EventoModel
{
    /**
     * Obtiene todos los eventos registrados en la base de datos
     * Devuelve los eventos ordenados por ID en orden descendente
     *
     * @return array Array asociativo con todos los eventos o array vacío si no hay registros
     */
    public function getAll()
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Ejecutar consulta para obtener todos los eventos
        $query = $db->query("SELECT * FROM evento ORDER BY id_evento DESC");

        // Retornar resultados como array asociativo
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo evento en la base de datos
     * Inserta los datos del evento y retorna el ID del registro creado
     *
     * @param string $nombre Nombre del evento
     * @param string $descripcion Descripción del evento
     * @param string $fecha Fecha del evento (formato: YYYY-MM-DD)
     * @param string $hora_inicio Hora de inicio (formato: HH:MM)
     * @param string $hora_fin Hora de finalización (formato: HH:MM)
     * @return int|false ID del evento creado o false si falla la inserción
     */
    public function create($nombre, $descripcion, $fecha, $hora_inicio, $hora_fin)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de inserción
        $sql = "INSERT INTO evento (nombre, descripcion, fecha, hora_inicio, hora_fin) "
            . "VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta con los parámetros
        $ok = $stmt->execute([$nombre, $descripcion, $fecha, $hora_inicio, $hora_fin]);

        // Si la inserción fue exitosa, retornar el ID del nuevo evento
        if ($ok) {
            return (int)$db->lastInsertId();
        }

        // Si falla, retornar false
        return false;
    }

    /**
     * Obtiene un evento específico por su ID
     * Busca un evento en la base de datos usando el ID proporcionado
     *
     * @param int $id ID del evento a buscar
     * @return array|false Array asociativo con los datos del evento o false si no existe
     */
    public function getById($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de búsqueda por ID
        $sql = "SELECT * FROM evento WHERE id_evento = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta con el ID del evento
        $stmt->execute([$id]);

        // Retornar el evento encontrado o false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos de un evento existente
     * Modifica el registro del evento con los nuevos valores proporcionados
     *
     * @param int $id ID del evento a actualizar
     * @param string $nombre Nuevo nombre del evento
     * @param string $descripcion Nueva descripción del evento
     * @param string $fecha Nueva fecha del evento (formato: YYYY-MM-DD)
     * @param string $hora_inicio Nueva hora de inicio (formato: HH:MM)
     * @param string $hora_fin Nueva hora de finalización (formato: HH:MM)
     * @return bool true si la actualización fue exitosa, false en caso contrario
     */
    public function update($id, $nombre, $descripcion, $fecha, $hora_inicio, $hora_fin)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de actualización
        $sql = "UPDATE evento SET nombre = ?, descripcion = ?, fecha = ?, "
            . "hora_inicio = ?, hora_fin = ? WHERE id_evento = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta y retornar resultado
        return $stmt->execute([$nombre, $descripcion, $fecha, $hora_inicio, $hora_fin, $id]);
    }

    /**
     * Elimina un evento de la base de datos
     * Borra el registro del evento especificado por su ID
     *
     * @param int $id ID del evento a eliminar
     * @return bool true si la eliminación fue exitosa, false en caso contrario
     */
    public function delete($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de eliminación
        $sql = "DELETE FROM evento WHERE id_evento = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta y retornar resultado
        return $stmt->execute([$id]);
    }
}

?>