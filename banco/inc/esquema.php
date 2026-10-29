<?php
// Tablas y procedimientos de Banco (se crean desde estado.php). Misma copia que sql/banco.sql.
// Todo va en utf8mb4_general_ci, la misma collation de la conexión (comun/conexion.php).
return [
<<<'SQL'
CREATE TABLE IF NOT EXISTS cuenta_clientes (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    usuario   VARCHAR(40)  NOT NULL UNIQUE,
    pwd_hash  VARCHAR(255) NOT NULL,
    nombres   VARCHAR(80)  NOT NULL,
    apellidos VARCHAR(80)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
<<<'SQL'
CREATE TABLE IF NOT EXISTS cuenta_abonos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha      DATE NOT NULL,
    metodo     VARCHAR(30) NOT NULL,
    monto      DECIMAL(12,0) NOT NULL,   -- positivo = consignación, negativo = retiro
    INDEX idx_cuenta_abonos_cliente (cliente_id, fecha),
    CONSTRAINT fk_cuenta_abonos_cliente FOREIGN KEY (cliente_id)
        REFERENCES cuenta_clientes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
<<<'SQL'
ALTER TABLE cuenta_clientes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
SQL,
<<<'SQL'
ALTER TABLE cuenta_abonos CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
SQL,
<<<'SQL'
DROP PROCEDURE IF EXISTS sp_cuenta_buscar_cliente
SQL,
<<<'SQL'
CREATE PROCEDURE sp_cuenta_buscar_cliente(IN p_usuario VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)
BEGIN
    SELECT id, pwd_hash, nombres, apellidos
    FROM cuenta_clientes
    WHERE usuario = p_usuario;
END
SQL,
<<<'SQL'
DROP PROCEDURE IF EXISTS sp_cuenta_registrar_cliente
SQL,
<<<'SQL'
CREATE PROCEDURE sp_cuenta_registrar_cliente(
    IN p_usuario   VARCHAR(40)  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_pwd_hash  VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_nombres   VARCHAR(80)  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_apellidos VARCHAR(80)  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
)
BEGIN
    -- Si dos personas registran el mismo usuario a la vez, devuelve id 0
    DECLARE EXIT HANDLER FOR 1062 SELECT 0 AS id;

    IF EXISTS (SELECT 1 FROM cuenta_clientes WHERE usuario = p_usuario) THEN
        SELECT 0 AS id;                    -- el usuario ya existe
    ELSE
        INSERT INTO cuenta_clientes (usuario, pwd_hash, nombres, apellidos)
        VALUES (p_usuario, p_pwd_hash, p_nombres, p_apellidos);
        SELECT LAST_INSERT_ID() AS id;     -- id del cliente nuevo
    END IF;
END
SQL,
<<<'SQL'
DROP PROCEDURE IF EXISTS sp_cuenta_listar_abonos
SQL,
<<<'SQL'
CREATE PROCEDURE sp_cuenta_listar_abonos(IN p_cliente_id INT)
BEGIN
    SELECT fecha, metodo, monto
    FROM cuenta_abonos
    WHERE cliente_id = p_cliente_id
    ORDER BY fecha DESC, id DESC;
END
SQL,
<<<'SQL'
DROP PROCEDURE IF EXISTS sp_cuenta_agregar_abono
SQL,
<<<'SQL'
CREATE PROCEDURE sp_cuenta_agregar_abono(
    IN p_cliente_id INT,
    IN p_fecha      DATE,
    IN p_metodo     VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_monto      DECIMAL(12,0)
)
BEGIN
    INSERT INTO cuenta_abonos (cliente_id, fecha, metodo, monto)
    VALUES (p_cliente_id, p_fecha, p_metodo, p_monto);
END
SQL,
<<<'SQL'
DROP PROCEDURE IF EXISTS sp_cuenta_consignar
SQL,
<<<'SQL'
CREATE PROCEDURE sp_cuenta_consignar(
    IN p_cliente_id INT,
    IN p_fecha      DATE,
    IN p_metodo     VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_monto      DECIMAL(12,0)
)
BEGIN
    DECLARE v_n     INT DEFAULT 0;
    DECLARE v_saldo DECIMAL(14,0) DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' AS resultado, 0 AS saldo;
    END;

    IF p_monto IS NULL OR p_monto <= 0 THEN
        SELECT 'monto_invalido' AS resultado, 0 AS saldo;
    ELSE
        START TRANSACTION;
        SELECT COUNT(*) INTO v_n FROM cuenta_clientes WHERE id = p_cliente_id FOR UPDATE;
        IF v_n = 0 THEN
            ROLLBACK;
            SELECT 'no_existe' AS resultado, 0 AS saldo;
        ELSE
            INSERT INTO cuenta_abonos (cliente_id, fecha, metodo, monto)
            VALUES (p_cliente_id, p_fecha, p_metodo, p_monto);
            SELECT COALESCE(SUM(monto), 0) INTO v_saldo FROM cuenta_abonos WHERE cliente_id = p_cliente_id;
            COMMIT;
            SELECT 'ok' AS resultado, v_saldo AS saldo;
        END IF;
    END IF;
END
SQL,
<<<'SQL'
DROP PROCEDURE IF EXISTS sp_cuenta_retirar
SQL,
<<<'SQL'
CREATE PROCEDURE sp_cuenta_retirar(
    IN p_cliente_id INT,
    IN p_fecha      DATE,
    IN p_metodo     VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_monto      DECIMAL(12,0)
)
BEGIN
    DECLARE v_n     INT DEFAULT 0;
    DECLARE v_saldo DECIMAL(14,0) DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' AS resultado, 0 AS saldo;
    END;

    IF p_monto IS NULL OR p_monto <= 0 THEN
        SELECT 'monto_invalido' AS resultado, 0 AS saldo;
    ELSE
        START TRANSACTION;
        -- Bloquea al cliente para que dos retiros a la vez no pasen del saldo
        SELECT COUNT(*) INTO v_n FROM cuenta_clientes WHERE id = p_cliente_id FOR UPDATE;
        IF v_n = 0 THEN
            ROLLBACK;
            SELECT 'no_existe' AS resultado, 0 AS saldo;
        ELSE
            SELECT COALESCE(SUM(monto), 0) INTO v_saldo FROM cuenta_abonos WHERE cliente_id = p_cliente_id;
            IF v_saldo < p_monto THEN
                ROLLBACK;
                SELECT 'saldo_insuficiente' AS resultado, v_saldo AS saldo;
            ELSE
                INSERT INTO cuenta_abonos (cliente_id, fecha, metodo, monto)
                VALUES (p_cliente_id, p_fecha, p_metodo, -p_monto);
                COMMIT;
                SELECT 'ok' AS resultado, v_saldo - p_monto AS saldo;
            END IF;
        END IF;
    END IF;
END
SQL,
];
