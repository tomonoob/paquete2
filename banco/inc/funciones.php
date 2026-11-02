<?php
session_start();
date_default_timezone_set('America/Bogota');
require __DIR__ . '/conexion.php';

define('DEMO_USUARIO', 'demo');
define('DEMO_CLAVE', '1234');

function limpiar($texto)
{
    return htmlspecialchars((string) $texto);
}

function pesos($numero)
{
    return '$ ' . number_format($numero, 0, ',', '.');
}

function fecha_corta($fecha)
{
    $meses = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
    $tiempo = strtotime($fecha);
    return date('j', $tiempo) . ' ' . $meses[date('n', $tiempo) - 1] . ' ' . date('Y', $tiempo);
}

function iniciar_sesion($modo, $id, $nombres, $apellidos)
{
    $_SESSION['modo'] = $modo;
    $_SESSION['cliente_id'] = $id;
    $_SESSION['primer_nombre'] = $nombres;
    $_SESSION['nombre_completo'] = $nombres . ' ' . $apellidos;
    header('Location: cuenta.php');
    exit;
}

function movimientos_demo()
{
    if (!isset($_SESSION['demo_movimientos'])) {
        $_SESSION['demo_movimientos'] = array(
            array('fecha' => '2026-10-20', 'metodo' => 'Transferencia', 'monto' => 150000),
            array('fecha' => '2026-10-05', 'metodo' => 'Efectivo', 'monto' => 80000),
            array('fecha' => '2026-09-18', 'metodo' => 'Nequi', 'monto' => 120000),
            array('fecha' => '2026-09-02', 'metodo' => 'Efectivo', 'monto' => 60000),
            array('fecha' => '2026-08-15', 'metodo' => 'Transferencia', 'monto' => 200000),
            array('fecha' => '2026-08-01', 'metodo' => 'Nequi', 'monto' => 90000)
        );
    }
    return $_SESSION['demo_movimientos'];
}

function obtener_movimientos()
{
    if ($_SESSION['modo'] == 'demo') {
        return movimientos_demo();
    }
    return llamar('CALL sp_cuenta_listar_abonos(?)', array($_SESSION['cliente_id']));
}

function calcular_saldo($movimientos)
{
    $saldo = 0;
    if ($movimientos === false) {
        return $saldo;
    }
    foreach ($movimientos as $movimiento) {
        $saldo = $saldo + $movimiento['monto'];
    }
    return $saldo;
}

function mover_dinero($tipo, $monto, $metodo)
{
    $fecha = date('Y-m-d');

    if ($_SESSION['modo'] == 'demo') {
        $movimientos = movimientos_demo();
        $saldo = calcular_saldo($movimientos);

        if ($tipo == 'retirar' && $monto > $saldo) {
            return 'Saldo insuficiente. Tu saldo es ' . pesos($saldo) . '.';
        }

        $valor = $monto;
        if ($tipo == 'retirar') {
            $valor = -$monto;
        }
        array_unshift($movimientos, array('fecha' => $fecha, 'metodo' => $metodo, 'monto' => $valor));
        $_SESSION['demo_movimientos'] = $movimientos;
        $saldo_nuevo = $saldo + $valor;
    } else {
        $procedimiento = 'sp_cuenta_consignar';
        if ($tipo == 'retirar') {
            $procedimiento = 'sp_cuenta_retirar';
        }

        $filas = llamar('CALL ' . $procedimiento . '(?,?,?,?)', array($_SESSION['cliente_id'], $fecha, $metodo, $monto));
        if ($filas === false || count($filas) == 0) {
            return 'No pudimos registrar el movimiento. Abre «Estado de la base de datos» y pulsa «Preparar base de datos».';
        }

        $saldo_nuevo = $filas[0]['saldo'];
        if ($filas[0]['resultado'] == 'saldo_insuficiente') {
            return 'Saldo insuficiente. Tu saldo es ' . pesos($saldo_nuevo) . '.';
        }
        if ($filas[0]['resultado'] != 'ok') {
            return 'No pudimos registrar el movimiento. Intenta de nuevo.';
        }
    }

    $texto = 'Consignaste ';
    if ($tipo == 'retirar') {
        $texto = 'Retiraste ';
    }
    $_SESSION['aviso'] = $texto . pesos($monto) . '. Tu saldo ahora es ' . pesos($saldo_nuevo) . '.';
    return '';
}
