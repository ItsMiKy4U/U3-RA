--Nueva Base de Datos
CREATE DATABASE IF NOT EXISTS chiral_tech
USE chiral_tech;

--Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--Tabla de categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT
);

--Tabla de productos (componentes)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    especificaciones TEXT, -- Para poner frecuencias, VRAM, etc.
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria_id INT,
    imagen_url VARCHAR(255),
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);
--Tabla de Pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'pagado', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

--Detalles del Pedido
CREATE TABLE detalles_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Inserta el usuario admin por defecto
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Admin Chiral', 'admin@chiraltech.com', '$2y$10$TUu...hash...aqui', 'admin');

-- Inserta categorías base
INSERT INTO categorias (nombre, descripcion) VALUES 
('Procesadores', 'CPUs AMD e Intel'),
('Tarjetas Gráficas', 'GPUs Radeon y GeForce'),
('Memoria RAM', 'Módulos DDR4 y DDR5'),
('Almacenamiento', 'SSDs NVMe y SATA');

-- Inserta productos de ejemplo
INSERT INTO productos (nombre, descripcion, especificaciones, precio, stock, categoria_id) VALUES 
('AMD Ryzen 5 5600X', 'Procesador de 6 núcleos ideal para gaming', 'AM4, 6 Cores, 12 Threads, 3.7 GHz', 3500.00, 15, 1),
('AMD Radeon RX 6600', 'Tarjeta gráfica para 1080p en Ultra', '8GB GDDR6, PCIe 4.0', 4800.00, 8, 2),
('Kit 32GB (2x16) RAM DDR4', 'Memoria de alto rendimiento', '3200MHz, CL16', 1600.00, 20, 3),
('NVIDIA GTX 1050', 'GPU para videjojuegos en 4K, 2GB GDDR5,PCIe 3.0',3500.00, 40,2);