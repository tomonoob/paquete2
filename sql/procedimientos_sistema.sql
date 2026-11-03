-- Procedimientos usados por la app. Corré esto DESPUÉS de migracion_facturas.sql
-- (varios de estos referencian las tablas `facturas` y `movimientos.numero_factura`
-- que esa migración crea).
--
-- De los procedimientos que ya tenías, se reusan tal cual:
--   - insertar_clientes7        (ya lo usaba registrar_cliente)
--   - mostrar_para_actualizar   (cliente por cédula)
--   - pagos_fechas              (no depende de cedula/numero_factura, sigue sirviendo)
-- No se tocan por no ser usados por ninguna pantalla actual (quedan disponibles):
--   - actualizar_celular, buscar_celular, buscar_cliente

-- ============================================================
-- Clientes
-- ============================================================

-- Bug arreglado: los parámetros direccion/email/celular tenían el mismo
-- nombre que las columnas, así que "SET direccion=direccion" (etc.) eran
-- auto-asignaciones que nunca cambiaban nada. Se renombran los parámetros.
DROP PROCEDURE IF EXISTS actualizar_uncliente;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `actualizar_uncliente`(
    `p_cedula` VARCHAR(12),
    `p_nombres` VARCHAR(20),
    `p_apellidos` VARCHAR(20),
    `p_direccion` VARCHAR(20),
    `p_email` VARCHAR(35),
    `p_celular` VARCHAR(10)
)
BEGIN
    UPDATE clientes
    SET nombres = p_nombres,
        apellidos = p_apellidos,
        direccion = p_direccion,
        email = p_email,
        celular = p_celular
    WHERE cedula = p_cedula;
END;

DROP PROCEDURE IF EXISTS mostrar_todos_clientes;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `mostrar_todos_clientes`()
BEGIN
    SELECT * FROM clientes;
END;

-- Actualizado: movimientos ya no tiene `cedula` (ver migracion_facturas.sql),
-- así que el pago se une al cliente a través de facturas.cedula.
DROP PROCEDURE IF EXISTS mostrar_pagos;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `mostrar_pagos`(IN `p_cedula` VARCHAR(30))
BEGIN
    SELECT m.valor_pagado, m.fecha, m.numero_factura
    FROM movimientos m
    JOIN facturas f ON f.numero_factura = m.numero_factura
    WHERE f.cedula COLLATE utf8mb4_general_ci = p_cedula COLLATE utf8mb4_general_ci
    ORDER BY m.fecha DESC;
END;

-- Actualizado: mismo motivo que mostrar_pagos.
DROP PROCEDURE IF EXISTS valor_pagado;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `valor_pagado`(IN `p_cedula` VARCHAR(30))
BEGIN
    SELECT
        c.nombres,
        c.apellidos,
        SUM(m.valor_pagado) AS total
    FROM clientes c
    JOIN facturas f ON f.cedula COLLATE utf8mb4_general_ci = c.cedula COLLATE utf8mb4_general_ci
    JOIN movimientos m ON m.numero_factura = f.numero_factura
    WHERE c.cedula = p_cedula
    GROUP BY c.cedula, c.nombres, c.apellidos;
END;

-- ============================================================
-- Facturas
-- ============================================================

DROP PROCEDURE IF EXISTS crear_factura;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `crear_factura`(
    IN `p_numero_factura` VARCHAR(20),
    IN `p_cedula` VARCHAR(30)
)
BEGIN
    INSERT INTO facturas (numero_factura, cedula) VALUES (p_numero_factura, p_cedula);
END;

DROP PROCEDURE IF EXISTS obtener_factura;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `obtener_factura`(IN `p_numero_factura` VARCHAR(20))
BEGIN
    SELECT f.numero_factura, f.cedula, f.fecha, c.nombres, c.apellidos
    FROM facturas f
    LEFT JOIN clientes c ON c.cedula COLLATE utf8mb4_general_ci = f.cedula COLLATE utf8mb4_general_ci
    WHERE f.numero_factura COLLATE utf8mb4_0900_ai_ci = p_numero_factura COLLATE utf8mb4_0900_ai_ci;
END;

DROP PROCEDURE IF EXISTS listar_facturas_cliente;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `listar_facturas_cliente`(IN `p_cedula` VARCHAR(30))
BEGIN
    SELECT f.numero_factura, f.fecha,
           COALESCE(SUM(p.cantidad * p.precio_unitario), 0) AS total
    FROM facturas f
    LEFT JOIN productos_factura p ON p.numero_factura = f.numero_factura
    WHERE f.cedula COLLATE utf8mb4_general_ci = p_cedula COLLATE utf8mb4_general_ci
    GROUP BY f.numero_factura, f.fecha
    ORDER BY f.fecha DESC;
END;

DROP PROCEDURE IF EXISTS registrar_pago;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `registrar_pago`(
    IN `p_numero_factura` VARCHAR(20),
    IN `p_valor_pagado` DECIMAL(10,2)
)
BEGIN
    INSERT INTO movimientos (numero_factura, valor_pagado) VALUES (p_numero_factura, p_valor_pagado);
END;

-- ============================================================
-- Productos de factura
-- ============================================================

DROP PROCEDURE IF EXISTS insertar_producto_factura;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `insertar_producto_factura`(
    IN `p_numero_factura` VARCHAR(20),
    IN `p_nombre_producto` VARCHAR(100),
    IN `p_cantidad` INT,
    IN `p_precio_unitario` DECIMAL(10,2)
)
BEGIN
    INSERT INTO productos_factura (numero_factura, nombre_producto, cantidad, precio_unitario)
    VALUES (p_numero_factura, p_nombre_producto, p_cantidad, p_precio_unitario);
END;

DROP PROCEDURE IF EXISTS mostrar_todos_productos;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `mostrar_todos_productos`()
BEGIN
    SELECT * FROM productos_factura ORDER BY numero_factura, id;
END;

DROP PROCEDURE IF EXISTS consultar_productos_por_factura;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `consultar_productos_por_factura`(IN `p_numero_factura` VARCHAR(20))
BEGIN
    SELECT * FROM productos_factura
    WHERE numero_factura COLLATE utf8mb4_0900_ai_ci = p_numero_factura COLLATE utf8mb4_0900_ai_ci
    ORDER BY id;
END;

DROP PROCEDURE IF EXISTS mostrar_producto_por_id;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `mostrar_producto_por_id`(IN `p_id` INT)
BEGIN
    SELECT * FROM productos_factura WHERE id = p_id;
END;

DROP PROCEDURE IF EXISTS actualizar_producto_factura;
CREATE DEFINER=`avnadmin`@`%` PROCEDURE `actualizar_producto_factura`(
    IN `p_id` INT,
    IN `p_numero_factura` VARCHAR(20),
    IN `p_nombre_producto` VARCHAR(100),
    IN `p_cantidad` INT,
    IN `p_precio_unitario` DECIMAL(10,2)
)
BEGIN
    UPDATE productos_factura
    SET numero_factura = p_numero_factura,
        nombre_producto = p_nombre_producto,
        cantidad = p_cantidad,
        precio_unitario = p_precio_unitario
    WHERE id = p_id;
END;
