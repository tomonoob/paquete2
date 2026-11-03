<?php
require 'inc/funciones.php';
exigir_sesion();

$tipo = 'consignar';
if (isset($_GET['tipo']) && $_GET['tipo'] == 'retirar') {
    $tipo = 'retirar';
}
if (isset($_POST['tipo']) && $_POST['tipo'] == 'retirar') {
    $tipo = 'retirar';
}

$aviso = tomar_aviso();
$formulario = tomar_formulario();
$valor = '';

if (isset($formulario['valor'])) {
    $valor = $formulario['valor'];
}

$cuenta = llamar('CALL sp_bf_consultar(?)', array($_SESSION['cliente_id']));
$saldo = 0;

if ($cuenta !== false && count($cuenta) > 0) {
    $saldo = (int) $cuenta[0]['saldo'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $valor = '';
    if (isset($_POST['valor'])) {
        $valor = trim($_POST['valor']);
    }

    $datos = array('valor' => $valor);

    if (!ctype_digit($valor) || (int) $valor < 1) {
        guardar_aviso('error', 'Escribe un valor entero mayor que cero, solo con números.');
        guardar_formulario($datos);
        header('Location: movimiento.php?tipo=' . $tipo);
        exit;
    }

    if ((int) $valor > BF_MAXIMO) {
        guardar_aviso('error', 'El valor máximo por movimiento es ' . pesos(BF_MAXIMO) . '.');
        guardar_formulario($datos);
        header('Location: movimiento.php?tipo=' . $tipo);
        exit;
    }

    if ($conexion == null) {
        guardar_aviso('error', BF_SIN_CONEXION);
        guardar_formulario($datos);
        header('Location: movimiento.php?tipo=' . $tipo);
        exit;
    }

    $procedimiento = 'CALL sp_bf_consignar(?,?)';
    if ($tipo == 'retirar') {
        $procedimiento = 'CALL sp_bf_retirar(?,?)';
    }

    $filas = llamar($procedimiento, array($_SESSION['cliente_id'], (int) $valor));

    if ($filas === false || count($filas) == 0) {
        guardar_aviso('error', 'No pudimos registrar el movimiento. Intenta de nuevo en un momento.');
        guardar_formulario($datos);
        header('Location: movimiento.php?tipo=' . $tipo);
        exit;
    }

    $resultado = $filas[0]['resultado'];
    $saldo_bd = (int) $filas[0]['saldo'];
    $ahorro_bd = (int) $filas[0]['ahorro'];

    if ($resultado == 'ok') {
        if ($tipo == 'retirar') {
            $texto = 'Retiraste ' . pesos($valor) . '. Tu nuevo saldo es ' . pesos($saldo_bd) . '. Tu ahorro sigue en ' . pesos($ahorro_bd) . '.';
        } else {
            $texto = 'Consignaste ' . pesos($valor) . '. Tu nuevo saldo es ' . pesos($saldo_bd) . '.';
        }

        guardar_aviso('ok', $texto);
        header('Location: cuenta.php');
        exit;
    }

    if ($resultado == 'no_existe') {
        $texto = 'No encontramos tu cuenta. Vuelve a ingresar.';
    } elseif ($resultado == 'valor_invalido') {
        $texto = 'El valor debe ser un número entero mayor que cero.';
    } elseif ($resultado == 'saldo_cero') {
        $texto = 'No tienes saldo disponible. Consigna para poder retirar.';
    } elseif ($resultado == 'saldo_insuficiente') {
        $texto = 'Saldo insuficiente. Tu saldo es ' . pesos($saldo_bd) . '.';
    } else {
        $texto = 'No pudimos registrar el movimiento. Intenta de nuevo en un momento.';
    }

    guardar_aviso('error', $texto);
    guardar_formulario($datos);
    header('Location: movimiento.php?tipo=' . $tipo);
    exit;
}

if ($tipo == 'retirar') {
    $titulo = 'Retirar';
    $titulo_pagina = 'Retirar dinero';
    $texto_pagina = 'Saca plata de tu saldo disponible.';
    $clase_boton = 'boton-coral';
    $texto_boton = 'Retirar';
} else {
    $titulo = 'Consignar';
    $titulo_pagina = 'Consignar dinero';
    $texto_pagina = 'Suma plata a tu saldo disponible.';
    $clase_boton = '';
    $texto_boton = 'Consignar';
}

include 'inc/cabecera.php';
?>
<main class="contenido">
    <a class="volver" href="cuenta.php">← Volver a mi cuenta</a>

    <h1><?= $titulo_pagina ?></h1>
    <p class="subtitulo"><?= $texto_pagina ?> Tu saldo actual es <b><?= pesos($saldo) ?></b>.</p>

    <?php mostrar_aviso($aviso); ?>

    <?php if ($tipo == 'retirar' && $saldo == 0): ?>
        <div class="aviso aviso-aviso" role="status">
            <span class="aviso-icono" aria-hidden="true">i</span>
            <p>No tienes saldo disponible. Consigna para poder retirar.</p>
        </div>
    <?php endif; ?>

    <form class="panel" method="post" action="movimiento.php?tipo=<?= $tipo ?>">
        <input type="hidden" name="tipo" value="<?= $tipo ?>">

        <label for="valor">Valor a <?= $tipo == 'retirar' ? 'retirar' : 'consignar' ?></label>
        <div class="campo-dinero">
            <span aria-hidden="true">$</span>
            <input type="text" id="valor" name="valor" inputmode="numeric" pattern="[0-9]*" placeholder="0" value="<?= limpiar($valor) ?>" required aria-describedby="ayuda-valor">
        </div>
        <p class="ayuda" id="ayuda-valor">Solo números enteros. Máximo <?= pesos(BF_MAXIMO) ?>.</p>

        <div class="chips" aria-label="Valores rápidos">
            <button type="button" class="chip-valor" data-valor="50000">$ 50.000</button>
            <button type="button" class="chip-valor" data-valor="100000">$ 100.000</button>
            <button type="button" class="chip-valor" data-valor="200000">$ 200.000</button>
        </div>

        <button type="submit" class="boton <?= $clase_boton ?>"><?= $texto_boton ?></button>
    </form>
<?php include 'inc/pie.php'; ?>
