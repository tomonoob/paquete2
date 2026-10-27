<?php
require 'config.php';

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

// Base de datos
$hayBD = false; $fila = null;
if ($db = conectar()) {
    try {
        $st = $db->prepare('SELECT id, pwd_hash, nombres, apellidos FROM clientes WHERE usuario = ?');
        $st->bind_param('s', $usuario);
        $st->execute();
        $fila = $st->get_result()->fetch_assoc();
        $hayBD = true;
    } catch (Throwable $ex) {
        $hayBD = false;   // por ejemplo, las tablas no existen todavía
    }
}

if (!$hayBD) volver('No hay conexión con la base de datos. Por ahora solo puedes entrar con la cuenta de prueba.');

if ($fila && password_verify($password, $fila['pwd_hash'])) {
    iniciar_sesion('db', (int)$fila['id'], $fila);
}
volver('Usuario o contraseña incorrectos.');
