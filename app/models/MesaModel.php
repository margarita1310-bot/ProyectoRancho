<?php

// Incluir la clase de conexión a base de datos
require_once 'Conexion.php';

/**
 * MesaModel
 * Clase encargada de gestionar las mesas del restaurante
 * Proporciona métodos para activar, desactivar, actualizar estado y obtener información de mesas
 */
class MesaModel
{
    /**
     * Activa una cantidad específica de mesas
     * Desactiva todas las mesas primero, luego activa las que correspondan según la cantidad
     *
     * @param int $cantidad Número de mesas a activar (desde mesa 1 hasta la cantidad especificada)
     * @return bool true si la operación fue exitosa, false en caso de error
     */
    public function activarMesas($cantidad)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        try {
            // Primero desactivar todas las mesas
            $sql = "UPDATE mesa SET activa = 0, estado = 'Disponible', id_cliente = NULL";
            $db->exec($sql);

            // Luego activar las mesas según la cantidad especificada
            $sql = "UPDATE mesa SET activa = 1, estado = 'Disponible', id_cliente = NULL "
                . "WHERE numero >= 1 AND numero <= ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$cantidad]);

            // Retornar true si todo fue exitoso
            return true;
        } catch (Exception $e) {
            // Si ocurre un error, retornar false
            return false;
        }
    }

    /**
     * Desactiva todas las mesas del restaurante
     * Establece el estado de todas las mesas a inactivas y disponibles
     *
     * @return bool true si la operación fue exitosa, false en caso contrario
     */
    public function desactivarTodasMesas()
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Ejecutar la actualización y retornar resultado
        $sql = "UPDATE mesa SET activa = 0, estado = 'Disponible', id_cliente = NULL";
        return $db->exec($sql) !== false;
    }

    /**
     * Obtiene todas las mesas activas con información del cliente
     * Retorna mesas ordenadas por número, incluyendo datos del cliente si está ocupada
     *
     * @return array Array de arrays asociativos con datos de mesas activas y clientes
     */
    public function getMesasActivas()
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta con LEFT JOIN para obtener datos del cliente
        $sql = "SELECT m.*, c.nombre, c.telefono "
            . "FROM mesa m "
            . "LEFT JOIN cliente c ON m.id_cliente = c.id_cliente "
            . "WHERE m.activa = 1 "
            . "ORDER BY m.numero ASC";

        // Ejecutar la consulta y retornar resultados
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una mesa específica por su ID
     * Retorna los datos de la mesa junto con información del cliente si está asignado
     *
     * @param int $id ID de la mesa a buscar
     * @return array|false Array asociativo con datos de la mesa o false si no existe
     */
    public function getMesaById($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta con LEFT JOIN para obtener datos del cliente
        $sql = "SELECT m.*, c.nombre "
            . "FROM mesa m "
            . "LEFT JOIN cliente c ON m.id_cliente = c.id_cliente "
            . "WHERE m.id_mesa = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta con el ID de la mesa
        $stmt->execute([$id]);

        // Retornar la mesa encontrada o false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el estado de una mesa
     * Modifica el estado y asigna/desasigna un cliente a la mesa
     *
     * @param int $id ID de la mesa a actualizar
     * @param string $estado Nuevo estado de la mesa (ej: 'Disponible', 'Ocupada')
     * @param int|null $idCliente ID del cliente a asignar (null para desasignar)
     * @return bool true si la actualización fue exitosa, false en caso contrario
     */
    public function actualizarEstado($id, $estado, $idCliente = null)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar la consulta de actualización
        $sql = "UPDATE mesa SET estado = ?, id_cliente = ? WHERE id_mesa = ?";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta y retornar resultado
        return $stmt->execute([$estado, $idCliente, $id]);
    }

    /**
     * Libera una mesa (la marca como disponible)
     * Actualiza el estado a 'Disponible' y desasigna el cliente
     *
     * @param int $id ID de la mesa a liberar
     * @return bool true si la operación fue exitosa, false en caso contrario
     */
    public function liberarMesa($id)
    {
        // Llamar a actualizarEstado para marcar la mesa como disponible
        return $this->actualizarEstado($id, 'Disponible', null);
    }

    /**
     * Verifica si una fecha tiene reservas activas
     * Consulta si existen reservas pendientes o confirmadas para una fecha específica
     *
     * @param string $fecha Fecha a verificar (formato: YYYY-MM-DD)
     * @return bool true si existen reservas activas en esa fecha, false en caso contrario
     */
    public function tieneReservasActivas($fecha)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta para contar reservas activas en la fecha
        $sql = "SELECT COUNT(*) as total FROM reserva "
            . "WHERE fecha = ? AND estado IN ('pendiente', 'confirmada')";
        $stmt = $db->prepare($sql);

        // Ejecutar la consulta con la fecha
        $stmt->execute([$fecha]);

        // Obtener el resultado y verificar si hay reservas
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
}

?>
