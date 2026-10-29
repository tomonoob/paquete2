<?php
require __DIR__ . '/inc/config.php';
if (!isset($_SESSION['modo'])) { header('Location: index.php'); exit; }

$esRetiro = (($_POST['tipo'] ?? $_GET['tipo'] ?? '') === 'retirar');
$metodos  = $esRetiro
    ? ['Cajero automático', 'Oficina', 'Transferencia', 'Nequi']
    : ['Efectivo', 'Transferencia', 'Nequi'];
$error = ''; $monto = ''; $metodo = $metodos[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monto  = preg_replace('/\s+/', '', (string)($_POST['monto'] ?? ''));
    $metodo = (string)($_POST['metodo'] ?? '');

    if (!in_array($metodo, $metodos, true)) {
        $error = 'Elige un método válido.';
    } elseif (!preg_match('/^[0-9]{1,10}$/', $monto) || (int)$monto <= 0) {
        $error = 'Escribe un valor entero mayor que cero, solo con números.';
    } elseif ((int)$monto > 1000000000) {
        $error = 'El valor máximo por movimiento es ' . pesos(1000000000) . '.';
    } else {
        $r = mover_dinero($esRetiro ? 'retiro' : 'consignacion', (int)$monto, $metodo);
        if ($r['ok']) {
            $_SESSION['aviso'] = $r['msg'];
            header('Location: cuenta.php');
            exit;
        }
        $error = $r['msg'];
    }
}

$m = movimientos_cliente();
$saldo = saldo_de($m['filas']);

$titulo = ($esRetiro ? 'Retirar' : 'Consignar') . ' · Cuenta';
include __DIR__ . '/inc/cabecera.php';
?>
<main>
  <section class="login">
    <h1><?= $esRetiro ? 'Retirar dinero' : 'Consignar dinero' ?></h1>
    <p class="sub">Tu saldo actual es <b><?= e(pesos($saldo)) ?></b>.</p>
    <form action="movimiento.php" method="post">
      <input type="hidden" name="tipo" value="<?= $esRetiro ? 'retirar' : 'consignar' ?>">
      <label for="monto">Valor</label>
      <input id="monto" name="monto" type="number" inputmode="numeric" min="1" max="1000000000" step="1"
             placeholder="Ej. 50000" value="<?= e($monto) ?>" required>
      <label for="metodo"><?= $esRetiro ? 'Retirar por' : 'Método de pago' ?></label>
      <select id="metodo" name="metodo">
        <?php foreach ($metodos as $op): ?>
          <option value="<?= e($op) ?>"<?= $op === $metodo ? ' selected' : '' ?>><?= e($op) ?></option>
        <?php endforeach; ?>
      </select>
      <p class="error" role="alert"><?= e($error) ?></p>
      <button class="primary" type="submit"><?= $esRetiro ? 'Retirar' : 'Consignar' ?></button>
    </form>
    <p class="cambio"><a href="cuenta.php">Volver a mi cuenta</a></p>
  </section>
<?php include __DIR__ . '/inc/pie.php'; ?>
