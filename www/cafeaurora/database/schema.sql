CREATE DATABASE IF NOT EXISTS cafeaurora
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE cafeaurora;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('admin', 'editor') NOT NULL DEFAULT 'editor',
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  categoria VARCHAR(60) NOT NULL,
  descripcion VARCHAR(255) NOT NULL,
  precio DECIMAL(8,2) UNSIGNED NOT NULL,
  disponible TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_productos_categoria (categoria),
  INDEX idx_productos_disponible (disponible)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre_cliente VARCHAR(100) NOT NULL,
  correo VARCHAR(190) NOT NULL,
  telefono VARCHAR(30) NOT NULL,
  fecha DATE NOT NULL,
  hora TIME NOT NULL,
  personas TINYINT UNSIGNED NOT NULL,
  mensaje VARCHAR(500) NOT NULL DEFAULT '',
  estado ENUM('pendiente', 'confirmada', 'cancelada') NOT NULL DEFAULT 'pendiente',
  producto_id INT UNSIGNED NULL,
  creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reservas_producto FOREIGN KEY (producto_id)
    REFERENCES productos(id) ON DELETE SET NULL,
  INDEX idx_reservas_fecha (fecha, hora),
  INDEX idx_reservas_correo (correo)
) ENGINE=InnoDB;

INSERT INTO productos (nombre, categoria, descripcion, precio, disponible)
SELECT 'Americano', 'Café', 'Espresso intenso con agua caliente.', 42.00, 1
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE nombre = 'Americano');
INSERT INTO productos (nombre, categoria, descripcion, precio, disponible)
SELECT 'Latte Aurora', 'Café', 'Espresso suave con leche cremosa.', 58.00, 1
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE nombre = 'Latte Aurora');
INSERT INTO productos (nombre, categoria, descripcion, precio, disponible)
SELECT 'Chocolate de la casa', 'Bebidas', 'Chocolate caliente con un toque de canela.', 55.00, 1
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE nombre = 'Chocolate de la casa');
INSERT INTO productos (nombre, categoria, descripcion, precio, disponible)
SELECT 'Pan de elote', 'Panadería', 'Rebanada horneada en casa, dulce y húmeda.', 38.00, 1
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE nombre = 'Pan de elote');
 