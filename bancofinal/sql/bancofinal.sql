-- Banco Aurora - Trabajo final de Computacion Movil
-- Ejecutar completo en MySQL Workbench sobre la base de datos del proyecto.
-- No borra tablas: solo crea las que faltan y reemplaza los procedimientos sp_bf_.

SET NAMES utf8mb4 COLLATE utf8mb4_general_ci;

-- ------------------------------------------------------------
-- Tablas
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS bf_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(40) NOT NULL UNIQUE,
    pwd_hash VARCHAR(255) NOT NULL,
    nombres VARCHAR(80) NOT NULL,
    apellidos VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS ahorros (
    id INT NOT NULL PRIMARY KEY,
    saldo DECIMAL(12,0) NOT NULL DEFAULT 0,
    ahorro DECIMAL(12,0) NOT NULL DEFAULT 0,
    CONSTRAINT fk_ahorros_usuario FOREIGN KEY (id) REFERENCES bf_usuarios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS bf_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha DATETIME NOT NULL,
    tipo ENUM('ahorro', 'consignacion', 'retiro') NOT NULL,
    valor DECIMAL(12,0) NOT NULL,
    saldo_despues DECIMAL(12,0) NOT NULL,
    ahorro_despues DECIMAL(12,0) NOT NULL,
    CONSTRAINT fk_historial_ahorro FOREIGN KEY (cliente_id) REFERENCES ahorros (id) ON DELETE CASCADE,
    INDEX idx_historial_cliente (cliente_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- El historial tambien guarda los envios entre clientes y los retiros del ahorro.
-- Se puede ejecutar varias veces: solo amplia los valores permitidos.
ALTER TABLE bf_historial
    MODIFY tipo ENUM('ahorro', 'consignacion', 'retiro', 'envio', 'recibido', 'ahorro_retiro') NOT NULL;

-- ------------------------------------------------------------
-- Procedimientos (solo se reemplazan los sp_bf_)
-- ------------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_bf_buscar_usuario;
DROP PROCEDURE IF EXISTS sp_bf_registrar;
DROP PROCEDURE IF EXISTS sp_bf_consultar;
DROP PROCEDURE IF EXISTS sp_bf_ahorrar;
DROP PROCEDURE IF EXISTS sp_bf_consignar;
DROP PROCEDURE IF EXISTS sp_bf_retirar;
DROP PROCEDURE IF EXISTS sp_bf_sacar_ahorro;
DROP PROCEDURE IF EXISTS sp_bf_enviar;
DROP PROCEDURE IF EXISTS sp_bf_historial;

DELIMITER $$

CREATE PROCEDURE sp_bf_buscar_usuario(
    IN p_usuario VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
)
BEGIN
    SELECT id, pwd_hash, nombres, apellidos
    FROM bf_usuarios
    WHERE usuario = p_usuario;
END$$

CREATE PROCEDURE sp_bf_registrar(
    IN p_usuario VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_pwd_hash VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_nombres VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_apellidos VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
)
BEGIN
    DECLARE v_id INT DEFAULT 0;
    DECLARE v_existe INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 AS id;
    END;

    START TRANSACTION;

    SELECT COUNT(*) INTO v_existe
    FROM bf_usuarios
    WHERE usuario = p_usuario;

    IF v_existe > 0 THEN
        ROLLBACK;
        SELECT 0 AS id;
    ELSE
        INSERT INTO bf_usuarios (usuario, pwd_hash, nombres, apellidos)
        VALUES (p_usuario, p_pwd_hash, p_nombres, p_apellidos);

        SET v_id = LAST_INSERT_ID();

        INSERT INTO ahorros (id, saldo, ahorro)
        VALUES (v_id, 500000, 0);

        COMMIT;
        SELECT v_id AS id;
    END IF;
END$$

CREATE PROCEDURE sp_bf_consultar(IN p_id INT)
BEGIN
    SELECT saldo, ahorro
    FROM ahorros
    WHERE id = p_id;
END$$

CREATE PROCEDURE sp_bf_ahorrar(
    IN p_id INT,
    IN p_valor DECIMAL(12,0)
)
BEGIN
    DECLARE v_saldo DECIMAL(12,0) DEFAULT NULL;
    DECLARE v_ahorro DECIMAL(12,0) DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' AS resultado, 0 AS saldo, 0 AS ahorro;
    END;

    START TRANSACTION;

    SELECT saldo, ahorro INTO v_saldo, v_ahorro
    FROM ahorros
    WHERE id = p_id
    FOR UPDATE;

    IF v_saldo IS NULL THEN
        ROLLBACK;
        SELECT 'no_existe' AS resultado, 0 AS saldo, 0 AS ahorro;
    ELSEIF p_valor IS NULL OR p_valor <= 0 THEN
        ROLLBACK;
        SELECT 'valor_invalido' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF v_saldo = 0 THEN
        ROLLBACK;
        SELECT 'saldo_cero' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF p_valor > v_saldo THEN
        ROLLBACK;
        SELECT 'saldo_insuficiente' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSE
        SET v_saldo = v_saldo - p_valor;
        SET v_ahorro = v_ahorro + p_valor;

        UPDATE ahorros
        SET saldo = v_saldo, ahorro = v_ahorro
        WHERE id = p_id;

        INSERT INTO bf_historial (cliente_id, fecha, tipo, valor, saldo_despues, ahorro_despues)
        VALUES (p_id, NOW(), 'ahorro', p_valor, v_saldo, v_ahorro);

        COMMIT;
        SELECT 'ok' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    END IF;
END$$

CREATE PROCEDURE sp_bf_consignar(
    IN p_id INT,
    IN p_valor DECIMAL(12,0)
)
BEGIN
    DECLARE v_saldo DECIMAL(12,0) DEFAULT NULL;
    DECLARE v_ahorro DECIMAL(12,0) DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' AS resultado, 0 AS saldo, 0 AS ahorro;
    END;

    START TRANSACTION;

    SELECT saldo, ahorro INTO v_saldo, v_ahorro
    FROM ahorros
    WHERE id = p_id
    FOR UPDATE;

    IF v_saldo IS NULL THEN
        ROLLBACK;
        SELECT 'no_existe' AS resultado, 0 AS saldo, 0 AS ahorro;
    ELSEIF p_valor IS NULL OR p_valor <= 0 THEN
        ROLLBACK;
        SELECT 'valor_invalido' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSE
        SET v_saldo = v_saldo + p_valor;

        UPDATE ahorros
        SET saldo = v_saldo
        WHERE id = p_id;

        INSERT INTO bf_historial (cliente_id, fecha, tipo, valor, saldo_despues, ahorro_despues)
        VALUES (p_id, NOW(), 'consignacion', p_valor, v_saldo, v_ahorro);

        COMMIT;
        SELECT 'ok' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    END IF;
END$$

CREATE PROCEDURE sp_bf_retirar(
    IN p_id INT,
    IN p_valor DECIMAL(12,0)
)
BEGIN
    DECLARE v_saldo DECIMAL(12,0) DEFAULT NULL;
    DECLARE v_ahorro DECIMAL(12,0) DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' AS resultado, 0 AS saldo, 0 AS ahorro;
    END;

    START TRANSACTION;

    SELECT saldo, ahorro INTO v_saldo, v_ahorro
    FROM ahorros
    WHERE id = p_id
    FOR UPDATE;

    IF v_saldo IS NULL THEN
        ROLLBACK;
        SELECT 'no_existe' AS resultado, 0 AS saldo, 0 AS ahorro;
    ELSEIF p_valor IS NULL OR p_valor <= 0 THEN
        ROLLBACK;
        SELECT 'valor_invalido' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF v_saldo = 0 THEN
        ROLLBACK;
        SELECT 'saldo_cero' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF p_valor > v_saldo THEN
        ROLLBACK;
        SELECT 'saldo_insuficiente' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSE
        SET v_saldo = v_saldo - p_valor;

        UPDATE ahorros
        SET saldo = v_saldo
        WHERE id = p_id;

        INSERT INTO bf_historial (cliente_id, fecha, tipo, valor, saldo_despues, ahorro_despues)
        VALUES (p_id, NOW(), 'retiro', p_valor, v_saldo, v_ahorro);

        COMMIT;
        SELECT 'ok' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    END IF;
END$$

CREATE PROCEDURE sp_bf_sacar_ahorro(
    IN p_id INT,
    IN p_valor DECIMAL(12,0)
)
BEGIN
    DECLARE v_saldo DECIMAL(12,0) DEFAULT NULL;
    DECLARE v_ahorro DECIMAL(12,0) DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' AS resultado, 0 AS saldo, 0 AS ahorro;
    END;

    START TRANSACTION;

    SELECT saldo, ahorro INTO v_saldo, v_ahorro
    FROM ahorros
    WHERE id = p_id
    FOR UPDATE;

    IF v_saldo IS NULL THEN
        ROLLBACK;
        SELECT 'no_existe' AS resultado, 0 AS saldo, 0 AS ahorro;
    ELSEIF p_valor IS NULL OR p_valor <= 0 THEN
        ROLLBACK;
        SELECT 'valor_invalido' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF v_ahorro = 0 THEN
        ROLLBACK;
        SELECT 'ahorro_cero' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF p_valor > v_ahorro THEN
        ROLLBACK;
        SELECT 'ahorro_insuficiente' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSE
        SET v_saldo = v_saldo + p_valor;
        SET v_ahorro = v_ahorro - p_valor;

        UPDATE ahorros
        SET saldo = v_saldo, ahorro = v_ahorro
        WHERE id = p_id;

        INSERT INTO bf_historial (cliente_id, fecha, tipo, valor, saldo_despues, ahorro_despues)
        VALUES (p_id, NOW(), 'ahorro_retiro', p_valor, v_saldo, v_ahorro);

        COMMIT;
        SELECT 'ok' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    END IF;
END$$

CREATE PROCEDURE sp_bf_enviar(
    IN p_origen INT,
    IN p_destino INT,
    IN p_valor DECIMAL(12,0)
)
BEGIN
    DECLARE v_saldo DECIMAL(12,0) DEFAULT NULL;
    DECLARE v_ahorro DECIMAL(12,0) DEFAULT NULL;
    DECLARE v_saldo_destino DECIMAL(12,0) DEFAULT NULL;
    DECLARE v_ahorro_destino DECIMAL(12,0) DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'error' AS resultado, 0 AS saldo, 0 AS ahorro;
    END;

    START TRANSACTION;

    SELECT saldo, ahorro INTO v_saldo, v_ahorro
    FROM ahorros
    WHERE id = p_origen
    FOR UPDATE;

    IF v_saldo IS NULL THEN
        ROLLBACK;
        SELECT 'no_existe' AS resultado, 0 AS saldo, 0 AS ahorro;
    ELSEIF p_valor IS NULL OR p_valor <= 0 THEN
        ROLLBACK;
        SELECT 'valor_invalido' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF p_destino IS NULL OR p_destino = p_origen THEN
        ROLLBACK;
        SELECT 'destino_invalido' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF v_saldo = 0 THEN
        ROLLBACK;
        SELECT 'saldo_cero' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSEIF p_valor > v_saldo THEN
        ROLLBACK;
        SELECT 'saldo_insuficiente' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
    ELSE
        SELECT saldo, ahorro INTO v_saldo_destino, v_ahorro_destino
        FROM ahorros
        WHERE id = p_destino
        FOR UPDATE;

        IF v_saldo_destino IS NULL THEN
            ROLLBACK;
            SELECT 'destino_no_existe' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
        ELSE
            SET v_saldo = v_saldo - p_valor;
            SET v_saldo_destino = v_saldo_destino + p_valor;

            UPDATE ahorros
            SET saldo = v_saldo
            WHERE id = p_origen;

            UPDATE ahorros
            SET saldo = v_saldo_destino
            WHERE id = p_destino;

            INSERT INTO bf_historial (cliente_id, fecha, tipo, valor, saldo_despues, ahorro_despues)
            VALUES (p_origen, NOW(), 'envio', p_valor, v_saldo, v_ahorro);

            INSERT INTO bf_historial (cliente_id, fecha, tipo, valor, saldo_despues, ahorro_despues)
            VALUES (p_destino, NOW(), 'recibido', p_valor, v_saldo_destino, v_ahorro_destino);

            COMMIT;
            SELECT 'ok' AS resultado, v_saldo AS saldo, v_ahorro AS ahorro;
        END IF;
    END IF;
END$$

CREATE PROCEDURE sp_bf_historial(IN p_id INT)
BEGIN
    SELECT fecha, tipo, valor, saldo_despues, ahorro_despues
    FROM bf_historial
    WHERE cliente_id = p_id
    ORDER BY fecha DESC, id DESC
    LIMIT 50;
END$$

DELIMITER ;
