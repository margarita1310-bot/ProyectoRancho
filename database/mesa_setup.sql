-- Script SQL para configurar la tabla mesa
-- Este script asegura que la tabla mesa tenga la estructura correcta
-- y pre-carga 50 mesas en el sistema

-- Crear o modificar tabla mesa
CREATE TABLE IF NOT EXISTS `mesa` (
  `id_mesa` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) NOT NULL,
  `activa` tinyint(1) DEFAULT 0,
  `id_cliente` int(11) DEFAULT NULL,
  `estado` enum('Disponible','Ocupada') DEFAULT 'Disponible',
  PRIMARY KEY (`id_mesa`),
  UNIQUE KEY `numero` (`numero`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `fk_mesa_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar 50 mesas pre-definidas (si no existen)
INSERT IGNORE INTO `mesa` (`numero`, `activa`, `id_cliente`, `estado`) VALUES
(1, 0, NULL, 'Disponible'),
(2, 0, NULL, 'Disponible'),
(3, 0, NULL, 'Disponible'),
(4, 0, NULL, 'Disponible'),
(5, 0, NULL, 'Disponible'),
(6, 0, NULL, 'Disponible'),
(7, 0, NULL, 'Disponible'),
(8, 0, NULL, 'Disponible'),
(9, 0, NULL, 'Disponible'),
(10, 0, NULL, 'Disponible'),
(11, 0, NULL, 'Disponible'),
(12, 0, NULL, 'Disponible'),
(13, 0, NULL, 'Disponible'),
(14, 0, NULL, 'Disponible'),
(15, 0, NULL, 'Disponible'),
(16, 0, NULL, 'Disponible'),
(17, 0, NULL, 'Disponible'),
(18, 0, NULL, 'Disponible'),
(19, 0, NULL, 'Disponible'),
(20, 0, NULL, 'Disponible'),
(21, 0, NULL, 'Disponible'),
(22, 0, NULL, 'Disponible'),
(23, 0, NULL, 'Disponible'),
(24, 0, NULL, 'Disponible'),
(25, 0, NULL, 'Disponible'),
(26, 0, NULL, 'Disponible'),
(27, 0, NULL, 'Disponible'),
(28, 0, NULL, 'Disponible'),
(29, 0, NULL, 'Disponible'),
(30, 0, NULL, 'Disponible'),
(31, 0, NULL, 'Disponible'),
(32, 0, NULL, 'Disponible'),
(33, 0, NULL, 'Disponible'),
(34, 0, NULL, 'Disponible'),
(35, 0, NULL, 'Disponible'),
(36, 0, NULL, 'Disponible'),
(37, 0, NULL, 'Disponible'),
(38, 0, NULL, 'Disponible'),
(39, 0, NULL, 'Disponible'),
(40, 0, NULL, 'Disponible'),
(41, 0, NULL, 'Disponible'),
(42, 0, NULL, 'Disponible'),
(43, 0, NULL, 'Disponible'),
(44, 0, NULL, 'Disponible'),
(45, 0, NULL, 'Disponible'),
(46, 0, NULL, 'Disponible'),
(47, 0, NULL, 'Disponible'),
(48, 0, NULL, 'Disponible'),
(49, 0, NULL, 'Disponible'),
(50, 0, NULL, 'Disponible');
-- Verificar estructura de tabla mesas_disponibilidad
-- (Esta tabla ya debería existir, pero verificamos su estructura)
CREATE TABLE IF NOT EXISTS `mesas_disponibilidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `cantidad` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
