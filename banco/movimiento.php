<?php
require 'inc/funciones.php';

if (!isset($_SESSION['modo'])) {
    header('Location: index.php');
    exit;
}

$tipo = 'consignar';
if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] == 'retirar') {
    $tipo = 'retirar';
}

if ($tipo == 'retirar') {
    $metodos = array('Cajero automático', 'Oficina', 'Transferencia', 'Nequi');
} else {
    $metodos = array('Efectivo', 'Transferencia', 'Nequi');
}

$monto = '';
$metodo = $metodos[0];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monto = trim($_POST['monto']);
    $metodo = $_POST['metodo'];

    if (!in_array($metodo, $metodos)) {
        $error = 'Elige un método válido.';
    } elseif (!ctype_digit($monto) || $monto < 1) {
        $error = 'Escribe un valor entero mayor que cero, solo con números.';
    } elseif ($monto > 1000000000) {
        $error = 'El valor máximo por movimiento es ' . pesos(1000000000) . '.';
    } else {
        $error = mover_dinero($tipo, (int) $monto, $metodo);
        if ($error == '') {
            header('Location: cuenta.php');
            exit;
        }
    }
}

$saldo = calcular_saldo(obtener_movimientos());

if ($tipo == 'retirar') {
    $titulo = 'Retirar';
} else {
    $titulo = 'Consignar';
}
include 'inc/cabecera.php';
?>
<main>
    <div class="tarjeta">
        <?php if ($tipo == 'retirar'): ?>
            <h1>Retirar dinero</h1>
        <?php else: ?>
            <h1>Consignar dinero</h1>
        <?php endif; ?>
        <p class="subtitulo">Tu saldo actual es <b><?= pesos($saldo) ?></b>.</p>

        <form method="post" action="movimiento.php">
            <input type="hidden" name="tipo" value="<?= $tipo ?>">

            <label for="monto">Valor</label>
            <input type="number" id="monto" name="monto" min="1" max="1000000000" placeholder="Ej. 50000" value="<?= limpiar($monto) ?>" required>

            <label for="metodo">Método</label>
            <select id="metodo" name="metodo">
                <?php foreach ($metodos as $opcion): ?>
                    <option value="<?= limpiar($opcion) ?>" <?php if ($opcion == $metodo) { echo 'selected'; } ?>><?= limpiar($opcion) ?></option>
                <?php endforeach; ?>
            </select>

            <p class="error"><?= limpiar($error) ?></p>
            <button type="submit" class="boton"><?= $tipo == 'retirar' ? 'Retirar' : 'Consignar' ?></button>
        </form>

        <p class="enlace-centro"><a href="cuenta.php">Volver a mi cuenta</a></p>
    </div>
<?php include 'inc/pie.php'; ?>
