-- Banco Aurora - Usuarios de prueba
-- Ejecutar en MySQL Workbench DESPUES de bancofinal.sql.
-- Los tres usuarios tienen la contrasena 123456.
-- Se puede correr varias veces: si el usuario ya existe, sp_bf_registrar devuelve 0 y no pasa nada.

SET NAMES utf8mb4 COLLATE utf8mb4_general_ci;

CALL sp_bf_registrar('prueba1', '$2y$10$emBKjjpS7OM7M.QkaynTyeuy9j3gXnYrrpRas3H2Yzqobz17BBG8y', 'Valentina', 'Gómez Ríos');
CALL sp_bf_registrar('prueba2', '$2y$10$emBKjjpS7OM7M.QkaynTyeuy9j3gXnYrrpRas3H2Yzqobz17BBG8y', 'Andrés', 'Cárdenas');
CALL sp_bf_registrar('prueba3', '$2y$10$emBKjjpS7OM7M.QkaynTyeuy9j3gXnYrrpRas3H2Yzqobz17BBG8y', 'Juliana', 'Restrepo');

SELECT id, usuario, nombres, apellidos
FROM bf_usuarios
WHERE usuario IN ('prueba1', 'prueba2', 'prueba3');
