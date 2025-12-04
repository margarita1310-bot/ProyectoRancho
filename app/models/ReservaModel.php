<?php

// Incluir las clases necesarias
require_once 'Conexion.php';
require_once 'MesaModel.php';

/**
 * ReservaModel
 * Clase encargada de gestionar las reservas del sistema
 * Proporciona métodos para crear, confirmar, cancelar reservas y consultar disponibilidad de mesas
 */
class ReservaModel
{
    /**
     * Obtiene todas las reservas registradas en la base de datos
     * Devuelve las reservas con información del cliente usando LEFT JOIN
     *
     * @return array Array asociativo con todas las reservas o array vacío si no hay registros
     */
    public function getAll()
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta con JOIN para obtener datos del cliente
        $sql = "SELECT r.*, CONCAT(c.nombre, ' ') as nombre "
            . "FROM reserva r "
            . "LEFT JOIN cliente c ON r.id_cliente = c.id_cliente "
            . "ORDER BY r.id_reserva DESC";
        $stmt = $db->query($sql);

        // Retornar resultados como array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todas las reservas con estado 'pendiente'
     * Filtra las reservas que aún no han sido confirmadas
     *
     * @return array Array de reservas pendientes ordenadas por fecha y hora
     */
    public function getPending()
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta para obtener reservas pendientes
        $stmt = $db->prepare("SELECT * FROM reserva WHERE estado = 'pendiente' ORDER BY fecha, hora");

        // Ejecutar la consulta
        $stmt->execute();

        // Retornar resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las reservas de una fecha específica
     * Busca todas las reservas para una fecha determinada
     *
     * @param string $fecha Fecha a consultar (formato: YYYY-MM-DD)
     * @return array Array de reservas para esa fecha ordenadas por hora
     */
    public function getByDate($fecha)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta para obtener reservas por fecha
        $stmt = $db->prepare("SELECT * FROM reserva WHERE fecha = ? ORDER BY hora");

        // Ejecutar la consulta con la fecha
        $stmt->execute([$fecha]);

        // Retornar resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una reserva específica por su ID
     * Busca una reserva en la base de datos usando el ID proporcionado
     *
     * @param int $id ID de la reserva a buscar
     * @return array|false Array asociativo con los datos de la reserva o false si no existe
     */
    public function getById($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta de búsqueda por ID
        $stmt = $db->prepare("SELECT * FROM reserva WHERE id_reserva = ?");

        // Ejecutar la consulta con el ID
        $stmt->execute([$id]);

        // Retornar la reserva encontrada o false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Confirma una reserva y opcionalmente asigna una mesa
     * Usa transacciones para garantizar la consistencia de datos
     *
     * @param int $id ID de la reserva a confirmar
     * @param int $idMesa (Opcional) ID de la mesa a asignar a la reserva
     * @return bool true si la confirmación fue exitosa, false en caso contrario
     */
    public function confirm($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        try {
            // Iniciar transacción para garantizar consistencia
            $db->beginTransaction();

            // Obtener los datos de la reserva a confirmar
            $reserva = $this->getById($id);

            // Verificar que la reserva exista
            if (!$reserva) {
                $db->rollBack();
                return false;
            }

            // Verificar si se proporcionó un ID de mesa como segundo argumento
            if (func_num_args() >= 2) {
                $idMesa = func_get_arg(1);

                // Actualizar la reserva con el estado y la mesa asignada
                $stmt = $db->prepare("UPDATE reserva SET estado = 'confirmada', id_mesa = ? WHERE id_reserva = ?");
                $stmt->execute([$idMesa, $id]);

                // Actualizar el estado de la mesa a Ocupada
                $mesaModel = new MesaModel();
                $mesaModel->actualizarEstado($idMesa, 'Ocupada', $reserva['id_cliente']);
            } else {
                // Si no se proporciona mesa, solo cambiar el estado a confirmada
                $stmt = $db->prepare("UPDATE reserva SET estado = 'confirmada' WHERE id_reserva = ?");
                $stmt->execute([$id]);
            }

            // Confirmar la transacción
            $db->commit();

            // Retornar true si todo fue exitoso
            return true;
        } catch (Exception $e) {
            // Si ocurre un error, deshacer la transacción
            $db->rollBack();
            return false;
        }
    }

    /**
     * Elimina una reserva y libera la mesa asociada
     * Usa transacciones para asegurar que mesa y reserva se sincronicen
     *
     * @param int $id ID de la reserva a eliminar
     * @return bool true si la eliminación fue exitosa, false en caso contrario
     */
    public function delete($id)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        try {
            // Iniciar transacción para garantizar consistencia
            $db->beginTransaction();

            // Obtener los datos de la reserva a eliminar
            $reserva = $this->getById($id);

            // Si la reserva tiene mesa asignada, liberar la mesa
            if ($reserva && $reserva['id_mesa']) {
                $mesaModel = new MesaModel();
                $mesaModel->liberarMesa($reserva['id_mesa']);
            }

            // Eliminar la reserva
            $stmt = $db->prepare("DELETE FROM reserva WHERE id_reserva = ?");
            $stmt->execute([$id]);

            // Confirmar la transacción
            $db->commit();

            // Retornar true si todo fue exitoso
            return true;
        } catch (Exception $e) {
            // Si ocurre un error, deshacer la transacción
            $db->rollBack();
            return false;
        }
    }

    /**
     * Obtiene las mesas activas y disponibles para una fecha específica
     * Consulta disponibilidad, mesas activas y reservas existentes
     *
     * @param string $fecha Fecha a consultar (formato: YYYY-MM-DD)
     * @return array Array de mesas disponibles con id_mesa y numero, o array vacío si no hay disponibilidad
     * @throws Exception Si ocurre un error en la consulta
     */
    public function getMesasActivasYDisponibles($fecha)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        try {
            // Registrar en log el inicio de la búsqueda
            error_log("[getMesasActivasYDisponibles] Consultando disponibilidad para fecha: $fecha");

            // Incluir el modelo de disponibilidad
            require_once 'DisponibilidadModel.php';
            $dispModel = new DisponibilidadModel();

            // Obtener la disponibilidad configurada para la fecha
            $disponibilidad = $dispModel->getByDate($fecha);

            // Verificar que exista configuración de disponibilidad para la fecha
            if (!$disponibilidad || !isset($disponibilidad['cantidad'])) {
                error_log("[getMesasActivasYDisponibles] No hay disponibilidad configurada para $fecha");
                return [];
            }

            // Obtener la cantidad de mesas permitidas
            $cantidadPermitida = (int)$disponibilidad['cantidad'];
            error_log("[getMesasActivasYDisponibles] Cantidad permitida: $cantidadPermitida");

            // Consultar las mesas activas hasta el límite de cantidad permitida
            $sql = "SELECT id_mesa, numero "
                . "FROM mesa "
                . "WHERE activa = 1 "
                . "ORDER BY numero ASC "
                . "LIMIT " . (int)$cantidadPermitida;
            $stmt = $db->query($sql);
            $mesasActivas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("[getMesasActivasYDisponibles] Mesas activas encontradas: " . count($mesasActivas));

            // Si no hay mesas activas, retornar array vacío
            if (empty($mesasActivas)) {
                error_log("[getMesasActivasYDisponibles] No hay mesas activas en el catálogo");
                return [];
            }

            // Consultar las mesas que ya tienen reservas confirmadas o pendientes para esa fecha
            $sql = "SELECT id_mesa "
                . "FROM reserva "
                . "WHERE fecha = ? "
                . "AND estado IN ('pendiente', 'confirmada') "
                . "AND id_mesa IS NOT NULL";
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha]);
            $mesasReservadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("[getMesasActivasYDisponibles] Mesas ya reservadas: " . count($mesasReservadas));

            // Filtrar las mesas activas para obtener solo las que no están reservadas
            $mesasDisponibles = array_filter($mesasActivas, function ($mesa) use ($mesasReservadas) {
                return !in_array($mesa['id_mesa'], $mesasReservadas);
            });

            // Reindexar el array y retornar
            $resultado = array_values($mesasDisponibles);
            error_log("[getMesasActivasYDisponibles] Mesas disponibles finales: " . count($resultado));
            return $resultado;
        } catch (Exception $e) {
            // Si ocurre un error, registrarlo y relanzar la excepción
            error_log("[getMesasActivasYDisponibles] Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene las reservas de una fecha con información de mesas y disponibilidad
     * Retorna un array con estado de disponibilidad y lista de mesas con sus reservas
     *
     * @param string $fecha Fecha a consultar (formato: YYYY-MM-DD)
     * @return array Array con 'disponibilidad' (bool) y 'mesas' (array de mesas con reservas)
     * @throws Exception Si ocurre un error en la consulta
     */
    public function getReservasPorFechaConMesas($fecha)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        try {
            // Incluir el modelo de disponibilidad
            require_once 'DisponibilidadModel.php';
            $dispModel = new DisponibilidadModel();

            // Obtener la disponibilidad configurada para la fecha
            $disponibilidad = $dispModel->getByDate($fecha);

            // Si no hay disponibilidad para la fecha, retornar resultado vacío
            if (!$disponibilidad || !isset($disponibilidad['cantidad'])) {
                return ['disponibilidad' => false, 'mesas' => []];
            }

            // Obtener la cantidad de mesas permitidas
            $cantidadPermitida = (int)$disponibilidad['cantidad'];

            // Consultar las mesas activas hasta el límite de cantidad permitida
            $sql = "SELECT id_mesa, numero "
                . "FROM mesa "
                . "WHERE activa = 1 "
                . "ORDER BY numero ASC "
                . "LIMIT " . (int)$cantidadPermitida;
            $stmt = $db->query($sql);
            $mesasActivas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si no hay mesas activas, retornar disponibilidad pero sin mesas
            if (empty($mesasActivas)) {
                return ['disponibilidad' => true, 'mesas' => []];
            }

            // Consultar las reservas de la fecha con datos del cliente
            $sql = "SELECT r.*, c.nombre as cliente_nombre "
                . "FROM reserva r "
                . "LEFT JOIN cliente c ON r.id_cliente = c.id_cliente "
                . "WHERE r.fecha = ? "
                . "AND r.id_mesa IS NOT NULL";
            $stmt = $db->prepare($sql);
            $stmt->execute([$fecha]);
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Crear un mapeo de reservas por ID de mesa para acceso rápido
            $reservasPorMesa = [];
            foreach ($reservas as $reserva) {
                $reservasPorMesa[$reserva['id_mesa']] = $reserva;
            }

            // Construir el resultado final con información de cada mesa
            $resultado = [];
            foreach ($mesasActivas as $mesa) {
                // Inicializar fila de mesa con valores por defecto
                $fila = [
                    'id_mesa' => $mesa['id_mesa'],
                    'numero_mesa' => $mesa['numero'],
                    'tiene_reserva' => isset($reservasPorMesa[$mesa['id_mesa']]),
                    'id_reserva' => null,
                    'folio' => '-',
                    'cliente_nombre' => '-',
                    'hora' => '-',
                    'num_personas' => '-',
                    'estado' => null
                ];

                // Si la mesa tiene reserva, completar los datos de la reserva
                if (isset($reservasPorMesa[$mesa['id_mesa']])) {
                    $reserva = $reservasPorMesa[$mesa['id_mesa']];
                    $fila['id_reserva'] = $reserva['id_reserva'];
                    $fila['folio'] = $reserva['folio'] ?? '-';
                    $fila['cliente_nombre'] = $reserva['cliente_nombre'] ?? '-';
                    $fila['hora'] = $reserva['hora'] ?? '-';
                    $fila['num_personas'] = $reserva['num_personas'] ?? '-';
                    $fila['estado'] = $reserva['estado'];
                }

                // Agregar la fila de mesa al resultado
                $resultado[] = $fila;
            }

            // Retornar resultado con estado de disponibilidad y mesas
            return ['disponibilidad' => true, 'mesas' => $resultado];
        } catch (Exception $e) {
            // Si ocurre un error, registrarlo y relanzar la excepción
            error_log("[getReservasPorFechaConMesas] Error: " . $e->getMessage());
            throw $e;
        }
    }
}

?>
