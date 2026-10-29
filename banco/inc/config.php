<?php
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/db.php';

/* Si no hay conexión con la base de datos, la página funciona igual en modo de prueba:
   solo entra la cuenta demo (usuario "demo", contraseña "1234"). */
const DEMO_USUARIO  = 'demo';
const DEMO_PASSWORD = '1234';

// Datos de la cuenta de prueba (no necesitan base de datos).
function demo_nombre(): array {
    return ['nombres' => 'Camila', 'apellidos' => 'Torres'];
}

function demo_abonos(): array {
    return [
        ['fecha' => '2026-10-20', 'metodo' => 'Transferencia', 'monto' => 150000],
        ['fecha' => '2026-10-05', 'metodo' => 'Efectivo',      'monto' => 80000],
        ['fecha' => '2026-09-18', 'metodo' => 'Nequi',         'monto' => 120000],
        ['fecha' => '2026-09-02', 'metodo' => 'Efectivo',      'monto' => 60000],
        ['fecha' => '2026-08-15', 'metodo' => 'Transferencia', 'monto' => 200000],
        ['fecha' => '2026-08-01', 'metodo' => 'Nequi',         'monto' => 90000],
    ];
}

function e($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function pesos($n): string {
    return '$ ' . number_format((float)$n, 0, ',', '.');
}

function fecha_corta(string $f): string {
    static $m = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    try { $d = new DateTime($f); } catch (Throwable $ex) { return $f; }
    return $d->format('j') . ' ' . $m[(int)$d->format('n') - 1] . ' ' . $d->format('Y');
}

// Inicia la sesión del cliente y va a su cuenta. $modo: 'db' o 'demo'.
function iniciar_sesion(string $modo, int $id, array $n): void {
    session_regenerate_id(true);
    unset($_SESSION['ultimo_usuario']);
    $_SESSION['modo']       = $modo;
    $_SESSION['cliente_id'] = $id;
    $_SESSION['primer']     = $n['nombres'];
    $_SESSION['nombre']     = trim($n['nombres'] . ' ' . $n['apellidos']);
    header('Location: cuenta.php');
    exit;
}

require_once __DIR__ . '/movimientos.php';
