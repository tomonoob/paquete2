<?php
require 'inc/funciones.php';

if (!isset($_SESSION['modo'])) {
    header('Location: index.php');
    exit;
}

$movimientos = obtener_movimientos();
$saldo = calcular_saldo($movimientos);

$cantidad = 0;
if ($movimientos !== false) {
    $cantidad = count($movimientos);
}

$aviso = '';
if (isset($_SESSION['aviso'])) {
    $aviso = $_SESSION['aviso'];
    unset($_SESSION['aviso']);
}

$resumen = 'Todavía no tienes movimientos.';
if ($cantidad == 1) {
    $resumen = '1 movimiento, el último el ' . fecha_corta($movimientos[0]['fecha']);
} elseif ($cantidad > 1) {
    $resumen = $cantidad . ' movimientos, el último el ' . fecha_corta($movimientos[0]['fecha']);
}

$titulo = 'Mi cuenta';
include 'inc/cabecera.php';
?>
<main>
    <h1>Hola, <?= limpiar($_SESSION['primer_nombre']) ?></h1>
    <p class="subtitulo">Así va tu cuenta hasta hoy.</p>

    <?php if ($movimientos === false): ?>
        <p class="error">No pudimos cargar tus datos. Recarga la página en un momento.</p>
    <?php else: ?>

        <?php if ($aviso != ''): ?>
            <p class="aviso aviso-ok"><?= limpiar($aviso) ?></p>
        <?php endif; ?>

        <?php if ($_SESSION['modo'] == 'demo'): ?>
            <p class="aviso">Estás viendo la cuenta de prueba. Los movimientos son de ejemplo y se borran al salir.</p>
        <?php endif; ?>

        <div class="saldo-caja">
            <p>Total en tu cuenta</p>
            <span class="saldo" id="saldo" data-saldo="<?= $saldo ?>"><?= pesos($saldo) ?></span>
            <p><?= limpiar($resumen) ?></p>

            <div class="botones">
                <a class="btn-consignar" href="movimiento.php?tipo=consignar">Consignar</a>
                <a class="btn-retirar" href="movimiento.php?tipo=retirar">Retirar</a>
            </div>
        </div>

        <h2>Historial de movimientos</h2>
        <ul class="lista">
            <?php if ($cantidad == 0): ?>
                <li class="vacio">Cuando hagas tu primera consignación, aparecerá aquí.</li>
            <?php endif; ?>

            <?php foreach ($movimientos as $movimiento): ?>
                <li>
                    <span class="mov">
                        <?php if ($movimiento['monto'] < 0): ?>
                            <b>Retiro</b>
                        <?php else: ?>
                            <b>Consignación</b>
                        <?php endif; ?>
                        <small><?= fecha_corta($movimiento['fecha']) ?>, <?= limpiar($movimiento['metodo']) ?></small>
                    </span>

                    <?php if ($movimiento['monto'] < 0): ?>
                        <span class="monto resta">− <?= pesos(abs($movimiento['monto'])) ?></span>
                    <?php else: ?>
                        <span class="monto suma">+ <?= pesos($movimiento['monto']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>
<?php include 'inc/pie.php'; ?>
