-- Migración: relaciona clientes, facturas, productos_factura y movimientos (pagos).
--
-- Estado actual (antes de esta migración):
--   - productos_factura.numero_factura es texto libre, sin relación a ningún cliente.
--   - movimientos guarda pagos por `cedula` directamente, pero la tabla está vacía
--     y no hay ninguna pantalla en la app para insertar filas ahí.
--
-- Después de esta migración:
--   clientes (1) --- (N) facturas (1) --- (N) productos_factura
--                         |
--                         (1) --- (N) movimientos   (pagos por factura)
--
-- IMPORTANTE: hacé un respaldo de la base antes de correr esto.
-- Ejecutar contra tu base de Aiven (por consola web, phpMyAdmin, o `mysql` CLI).
-- No fue ejecutado automáticamente: este entorno no tiene la contraseña de la BD.

-- 1. Nueva tabla `facturas`: une un cliente con sus facturas.
--    `cedula` es NULLABLE a propósito: permite conservar facturas "huérfanas"
--    (como las 3 que ya existen en productos_factura sin cliente conocido) sin
--    perder datos ni romper la migración.
CREATE TABLE IF NOT EXISTS `facturas` (
  `numero_factura` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `cedula` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`numero_factura`),
  KEY `idx_facturas_cedula` (`cedula`),
  CONSTRAINT `fk_facturas_cliente` FOREIGN KEY (`cedula`) REFERENCES `clientes` (`cedula`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Backfill: crea una factura "sin cliente asignado" para cada numero_factura
--    que ya existe en productos_factura, para no romper la FK del paso 3.
--    Podés asignarles cliente después con:
--      UPDATE facturas SET cedula = '12345' WHERE numero_factura = 'F001';
INSERT INTO facturas (numero_factura, cedula, fecha)
SELECT numero_factura, NULL, MIN(fecha_registro)
FROM productos_factura
GROUP BY numero_factura;

-- 3. productos_factura ahora exige que su numero_factura exista en facturas.
ALTER TABLE productos_factura
  ADD CONSTRAINT `fk_productos_factura` FOREIGN KEY (`numero_factura`) REFERENCES `facturas` (`numero_factura`);

-- 4. Rediseñar `movimientos` para que los pagos se registren por factura
--    (no directo al cliente: el cliente se obtiene vía facturas.cedula).
--    La tabla está vacía hoy, así que este cambio no pierde datos.
ALTER TABLE movimientos
  DROP PRIMARY KEY,
  MODIFY `consecutivo` int NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`consecutivo`);

ALTER TABLE movimientos
  CHANGE `cedula` `numero_factura` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  MODIFY `valor_pagado` decimal(10,2) NOT NULL,
  MODIFY `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  ADD CONSTRAINT `fk_movimientos_factura` FOREIGN KEY (`numero_factura`) REFERENCES `facturas` (`numero_factura`);
