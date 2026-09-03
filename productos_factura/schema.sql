-- Ejecuta esto UNA sola vez en tu base de datos de Aiven (defaultdb)
-- antes de usar los nuevos módulos de productos de factura.

CREATE TABLE IF NOT EXISTS productos_factura (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura   VARCHAR(20)    NOT NULL,
    nombre_producto  VARCHAR(100)   NOT NULL,
    cantidad         INT            NOT NULL,
    precio_unitario  DECIMAL(10,2)  NOT NULL,
    fecha_registro   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_numero_factura (numero_factura)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo (opcional, bórralos si no los quieres)
INSERT INTO productos_factura (numero_factura, nombre_producto, cantidad, precio_unitario) VALUES
('F001', 'Teclado inalámbrico', 2, 45000.00),
('F001', 'Mouse óptico', 1, 25000.00),
('F002', 'Monitor 24"', 1, 480000.00);
