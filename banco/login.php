<?php
require __DIR__ . '/inc/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$usuario  = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
$_SESSION['ultimo_usuario'] = $usuario;

function volver(string $msg): void { $_SESSION['error'] = $msg; header('Location: index.php'); exit; }

if ($usuario === '' || $password === '') volver('Escribe tu usuario y tu contraseña.');

// La cuenta de prueba siempre está disponible, con o sin base de datos
if (hash_equals(DEMO_USUARIO, $usuario) && hash_equals(DEMO_PASSWORD, $password)) {
    iniciar_sesion('demo', -1, demo_nombre());
}

// Base de datos (todo por procedimientos almacenados)
$r = cuenta_llamar('CALL sp_cuenta_buscar_cliente(?)', [$usuario]);
if (!$r['ok']) {
    volver(conectar()
        ? 'La base de datos está conectada, pero faltan las tablas. Abre «Estado de la base de datos» para prepararla.'
        : 'No hay conexión con la base de datos. Por ahora solo puedes entrar con la cuenta de prueba.');
}

$fila = $r['filas'][0] ?? null;
if ($fila && password_verify($password, $fila['pwd_hash'])) {
    iniciar_sesion('db', (int)$fila['id'], $fila);
}
volver('Usuario o contraseña incorrectos.');
