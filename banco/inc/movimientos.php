<?php
/* Movimientos de la cuenta: consignar, retirar y listar.
   En cuenta_abonos, monto positivo = consignación y negativo = retiro.
   La cuenta de prueba (demo) guarda sus movimientos en la sesión, sin base de datos. */

function es_demo(): bool {
    return ($_SESSION['modo'] ?? '') === 'demo';
}

// Movimientos del cliente, el más reciente primero: ['ok' => bool, 'filas' => array]
function movimientos_cliente(): array {
    if (es_demo()) {
        if (!isset($_SESSION['demo_movs'])) $_SESSION['demo_movs'] = demo_abonos();
        return ['ok' => true, 'filas' => $_SESSION['demo_movs']];
    }
    $r = cuenta_llamar('CALL sp_cuenta_listar_abonos(?)', [$_SESSION['cliente_id']]);
    return ['ok' => $r['ok'], 'filas' => $r['filas']];
}

function saldo_de(array $filas): int {
    return (int)round(array_sum(array_column($filas, 'monto')));
}

/* Registra una consignación ('consignacion') o un retiro ('retiro').
   Devuelve ['ok' => bool, 'msg' => string]. El retiro nunca deja el saldo en negativo. */
function mover_dinero(string $tipo, int $monto, string $metodo): array {
    $retiro = ($tipo === 'retiro');
    if ($monto <= 0) return ['ok' => false, 'msg' => 'Escribe un valor mayor que cero.'];
    $fecha = date('Y-m-d');

    if (es_demo()) {
        $m = movimientos_cliente();
        $saldo = saldo_de($m['filas']);
        if ($retiro && $monto > $saldo) {
            return ['ok' => false, 'msg' => 'Saldo insuficiente. Tu saldo es ' . pesos($saldo) . '.'];
        }
        array_unshift($m['filas'], ['fecha' => $fecha, 'metodo' => $metodo, 'monto' => $retiro ? -$monto : $monto]);
        $_SESSION['demo_movs'] = $m['filas'];
        $nuevo = $saldo + ($retiro ? -$monto : $monto);
    } else {
        $sp = $retiro ? 'sp_cuenta_retirar' : 'sp_cuenta_consignar';
        $r = cuenta_llamar("CALL $sp(?,?,?,?)", [$_SESSION['cliente_id'], $fecha, $metodo, $monto]);
        if (!$r['ok']) {
            return ['ok' => false, 'msg' => 'No pudimos registrar el movimiento. Abre «Estado de la base de datos» y pulsa «Preparar base de datos».'];
        }
        $res = $r['filas'][0]['resultado'] ?? '';
        $nuevo = (int)($r['filas'][0]['saldo'] ?? 0);
        if ($res === 'saldo_insuficiente') {
            return ['ok' => false, 'msg' => 'Saldo insuficiente. Tu saldo es ' . pesos($nuevo) . '.'];
        }
        if ($res !== 'ok') {
            return ['ok' => false, 'msg' => 'No pudimos registrar el movimiento. Intenta de nuevo.'];
        }
    }

    $texto = $retiro ? 'Retiraste ' : 'Consignaste ';
    return ['ok' => true, 'msg' => $texto . pesos($monto) . '. Tu saldo ahora es ' . pesos($nuevo) . '.'];
}
