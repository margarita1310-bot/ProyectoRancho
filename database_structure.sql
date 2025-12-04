-- Estructura de Base de Datos para Rancho La Joya
-- Compatible con MySQL 5.7+
-- Crear este script basado en tu base de datos actual

-- Para exportar la estructura desde XAMPP:
-- 1. Abrir phpMyAdmin (http://localhost/phpmyadmin)
-- 2. Seleccionar la base de datos 'lajoya_gestion'
-- 3. Ir a la pestaña "Exportar"
-- 4. Seleccionar formato SQL
-- 5. Copiar el contenido generado aquí

-- Para importar en Infinity Free:
-- 1. Ir al panel de Infinity Free
-- 2. Abrir phpMyAdmin
-- 3. Seleccionar la base de datos creada
-- 4. Ir a "Importar"
-- 5. Subir este archivo
-- 6. Ejecutar

-- EJEMPLO DE ESTRUCTURA (Debes reemplazarlo con tu estructura actual):

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `correo` VARCHAR(255) UNIQUE NOT NULL,
  `contraseña` VARCHAR(255) NOT NULL,
  `rol` ENUM('admin', 'usuario') DEFAULT 'usuario',
  `activo` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `eventos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `descripcion` TEXT,
  `fecha` DATE NOT NULL,
  `hora_inicio` TIME,
  `hora_fin` TIME,
  `lugar` VARCHAR(255),
  `capacidad` INT,
  `imagen` VARCHAR(255),
  `activo` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `productos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `descripcion` TEXT,
  `precio` DECIMAL(10, 2) NOT NULL,
  `imagen` VARCHAR(255),
  `disponible` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `promociones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `descripcion` TEXT,
  `descuento` DECIMAL(5, 2),
  `fecha_inicio` DATE,
  `fecha_fin` DATE,
  `imagen` VARCHAR(255),
  `activa` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reservas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT,
  `fecha_reserva` DATE NOT NULL,
  `hora_reserva` TIME,
  `cantidad_personas` INT,
  `estado` ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
  `notas` TEXT,
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMPORTANTE: 
-- Reemplaza el contenido anterior con tu estructura real de base de datos
-- Puedes exportar desde XAMPP siguiendo los pasos indicados arriba
