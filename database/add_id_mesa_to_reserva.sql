-- Agregar columna id_mesa a la tabla reserva
-- Esta columna almacena el ID de la mesa asignada a la reserva

ALTER TABLE `reserva` 
ADD COLUMN `id_mesa` INT(11) NULL DEFAULT NULL AFTER `id_evento`,
ADD CONSTRAINT `fk_reserva_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `mesa` (`id_mesa`) ON DELETE SET NULL;

-- Verificar que se agregó correctamente
SELECT 'Columna id_mesa agregada exitosamente' AS resultado;
