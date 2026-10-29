<?php
require __DIR__ . '/inc/config.php';
if (!isset($_SESSION['modo'])) { header('Location: index.php'); exit; }

$m = movimientos_cliente();
$fallo = !$m['ok'];
$movs  = $m['filas'];
$total = saldo_de($movs);
$n = count($movs);
$aviso = $_SESSION['aviso'] ?? '';
unset($_SESSION['aviso']);

$titulo = 'Mi cuenta · Cuenta';
include __DIR__ . '/inc/cabecera.php';
?>
<main>
  <h1>Hola, <?= e($_SESSION['primer']) ?></h1>
  <p class="sub">Así va tu cuenta hasta hoy.</p>

  <?php if ($fallo): ?>
    <p class="error">No pudimos cargar tus datos. Recarga la página en un momento.</p>
  <?php else: ?>
  <?php if ($aviso): ?>
    <p class="aviso ok" role="status"><?= e($aviso) ?></p>
  <?php endif; ?>
  <?php if (es_demo()): ?>
    <p class="aviso">Estás viendo la cuenta de prueba. Los movimientos son de ejemplo y se borran al salir.</p>
  <?php endif; ?>
  <div class="balance">
    <p>Total en tu cuenta</p>
    <span class="total" id="total" data-total="<?= $total ?>"><?= pesos($total) ?></span>
    <p><?= $n
        ? $n . ($n === 1 ? ' movimiento' : ' movimientos') . ' · último el ' . e(fecha_corta($movs[0]['fecha']))
        : 'Todavía no tienes movimientos.' ?></p>
    <div class="acciones">
      <a class="acc-dep" href="movimiento.php?tipo=consignar">Consignar</a>
      <a class="acc-ret" href="movimiento.php?tipo=retirar">Retirar</a>
    </div>
  </div>

  <h2>Historial de movimientos</h2>
  <ul class="list">
    <?php if (!$n): ?>
      <li class="empty">Cuando hagas tu primera consignación, aparecerá aquí.</li>
    <?php endif; ?>
    <?php foreach ($movs as $a): $retiro = (float)$a['monto'] < 0; ?>
      <li>
        <span class="mov">
          <b><?= $retiro ? 'Retiro' : 'Consignación' ?></b>
          <small><?= e(fecha_corta($a['fecha'])) ?>, <?= e($a['metodo']) ?></small>
        </span>
        <span class="monto <?= $retiro ? 'menos' : 'mas' ?>"><?= $retiro ? '−' : '+' ?><?= e(pesos(abs((float)$a['monto']))) ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
<?php include __DIR__ . '/inc/pie.php'; ?>
