-- Insertar reservas de prueba para visualizar funcionalidad
-- Fecha: 2025-11-19 (hoy)

-- Insertar clientes de prueba
INSERT INTO cliente (nombre, telefono, email) VALUES
('Juan Pérez', '6441234567', 'juan.perez@email.com'),
('María González', '6449876543', 'maria.gonzalez@email.com'),
('Carlos Rodríguez', '6445551234', 'carlos.rodriguez@email.com'),
('Ana Martínez', '6447778888', 'ana.martinez@email.com'),
('Luis Hernández', '6443332222', 'luis.hernandez@email.com');

-- Insertar eventos de prueba
INSERT INTO evento (nombre, descripcion, fecha, hora, capacidad) VALUES
('Noche de Mariachi', 'Show de mariachi en vivo con cena incluida', '2025-11-19', '20:00:00', 50),
('Cena Romántica', 'Cena especial para parejas con música en vivo', '2025-11-20', '19:00:00', 30);

-- Insertar 3 reservas pendientes para hoy
INSERT INTO reserva (id_cliente, id_evento, id_mesa, fecha, hora, num_personas, estado, folio) VALUES
(1, NULL, NULL, '2025-11-19', '18:00:00', 4, 'pendiente', CONCAT('RES-', LPAD(FLOOR(RAND() * 99999), 5, '0'))),
(2, NULL, NULL, '2025-11-19', '19:30:00', 2, 'pendiente', CONCAT('RES-', LPAD(FLOOR(RAND() * 99999), 5, '0'))),
(3, NULL, NULL, '2025-11-19', '20:00:00', 6, 'pendiente', CONCAT('RES-', LPAD(FLOOR(RAND() * 99999), 5, '0')));

-- Insertar 2 reservas confirmadas (con mesa asignada)
INSERT INTO reserva (id_cliente, id_evento, id_mesa, fecha, hora, num_personas, estado, folio) VALUES
(4, NULL, 1, '2025-11-19', '17:00:00', 3, 'confirmada', CONCAT('RES-', LPAD(FLOOR(RAND() * 99999), 5, '0'))),
(5, NULL, 2, '2025-11-19', '21:00:00', 5, 'confirmada', CONCAT('RES-', LPAD(FLOOR(RAND() * 99999), 5, '0')));

-- Insertar 1 reserva cancelada
INSERT INTO reserva (id_cliente, id_evento, id_mesa, fecha, hora, num_personas, estado, folio) VALUES
(1, NULL, NULL, '2025-11-19', '16:00:00', 2, 'cancelada', CONCAT('RES-', LPAD(FLOOR(RAND() * 99999), 5, '0')));

-- Actualizar las mesas 1 y 2 como ocupadas (porque tienen reservas confirmadas)
UPDATE mesa SET estado = 'Ocupada', id_cliente = 4 WHERE id_mesa = 1;
UPDATE mesa SET estado = 'Ocupada', id_cliente = 5 WHERE id_mesa = 2;

-- Verificar datos insertados
SELECT 
    r.id_reserva,
    r.folio,
    c.nombre as cliente,
    r.fecha,
    r.hora,
    r.num_personas,
    r.estado,
    r.id_mesa
FROM reserva r
LEFT JOIN cliente c ON r.id_cliente = c.id_cliente
WHERE r.fecha = '2025-11-19'
ORDER BY r.hora;
