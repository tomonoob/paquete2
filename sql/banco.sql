CREATE TABLE IF NOT EXISTS cuenta_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(40) NOT NULL UNIQUE,
    pwd_hash VARCHAR(255) NOT NULL,
    nombres VARCHAR(80) NOT NULL,
    apellidos VARCHAR(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cuenta_abonos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha DATE NOT NULL,
    metodo VARCHAR(30) NOT NULL,
    monto DECIMAL(12,0) NOT NULL,
    FOREIGN KEY (cliente_id) REFERENCES cuenta_clientes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP PROCEDURE IF EXISTS sp_cuenta_buscar_cliente;

CREATE PROCEDURE sp_cuenta_buscar_cliente(IN p_usuario VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)
BEGIN
    SELECT id, pwd_hash, nombres, apellidos
    FROM cuenta_clientes
    WHERE usuario = p_usuario;
END;

DROP PROCEDURE IF EXISTS sp_cuenta_registrar_cliente;

CREATE PROCEDURE sp_cuenta_registrar_cliente(
    IN p_usuario VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_pwd_hash VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_nombres VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_apellidos VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
)
BEGIN
    IF EXISTS (SELECT 1 FROM cuenta_clientes WHERE usuario = p_usuario) THEN
        SELECT 0 AS id;
    ELSE
        INSERT INTO cuenta_clientes (usuario, pwd_hash, nombres, apellidos)
        VALUES (p_usuario, p_pwd_hash, p_nombres, p_apellidos);
        SELECT LAST_INSERT_ID() AS id;
    END IF;
END;

DROP PROCEDURE IF EXISTS sp_cuenta_listar_abonos;

CREATE PROCEDURE sp_cuenta_listar_abonos(IN p_cliente_id INT)
BEGIN
    SELECT fecha, metodo, monto
    FROM cuenta_abonos
    WHERE cliente_id = p_cliente_id
    ORDER BY fecha DESC, id DESC;
END;

DROP PROCEDURE IF EXISTS sp_cuenta_consignar;

CREATE PROCEDURE sp_cuenta_consignar(
    IN p_cliente_id INT,
    IN p_fecha DATE,
    IN p_metodo VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_monto DECIMAL(12,0)
)
BEGIN
    IF p_monto <= 0 THEN
        SELECT 'monto_invalido' AS resultado, 0 AS saldo;
    ELSE
        INSERT INTO cuenta_abonos (cliente_id, fecha, metodo, monto)
        VALUES (p_cliente_id, p_fecha, p_metodo, p_monto);
        SELECT 'ok' AS resultado, SUM(monto) AS saldo
        FROM cuenta_abonos
        WHERE cliente_id = p_cliente_id;
    END IF;
END;

DROP PROCEDURE IF EXISTS sp_cuenta_retirar;

CREATE PROCEDURE sp_cuenta_retirar(
    IN p_cliente_id INT,
    IN p_fecha DATE,
    IN p_metodo VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
    IN p_monto DECIMAL(12,0)
)
BEGIN
    DECLARE v_saldo DECIMAL(14,0);

    IF p_monto <= 0 THEN
        SELECT 'monto_invalido' AS resultado, 0 AS saldo;
    ELSE
        SELECT COALESCE(SUM(monto), 0) INTO v_saldo
        FROM cuenta_abonos
        WHERE cliente_id = p_cliente_id;

        IF v_saldo < p_monto THEN
            SELECT 'saldo_insuficiente' AS resultado, v_saldo AS saldo;
        ELSE
            INSERT INTO cuenta_abonos (cliente_id, fecha, metodo, monto)
            VALUES (p_cliente_id, p_fecha, p_metodo, -p_monto);
            SELECT 'ok' AS resultado, v_saldo - p_monto AS saldo;
        END IF;
    END IF;
END;
