<?php
require __DIR__ . '/inc/config.php';

$mensaje = ''; $exito = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        bd_preparar();
        $mensaje = 'Listo: las tablas y los procedimientos quedaron creados.';
        $exito = true;
    } catch (Throwable $ex) {
        $mensaje = conectar()
            ? 'No se pudo preparar la base de datos: ' . $ex->getMessage()
            : 'No hay conexión con la base de datos. ' . bd_motivo();
    }
}
$conectado = conectar() !== null;
[$listo, $detalle] = $conectado ? bd_revisar() : [false, ''];

$titulo = 'Estado de la base de datos · Cuenta';
include __DIR__ . '/inc/cabecera.php';
?>
<main>
  <section class="login">
    <h1>Base de datos</h1>
    <p class="sub">Aquí ves si Banco puede usar tu base de datos.</p>
    <?php if ($mensaje): ?>
      <p class="<?= $exito ? 'aviso ok' : 'error' ?>" role="status"><?= e($mensaje) ?></p>
    <?php endif; ?>
    <ul class="estados">
      <li class="<?= $conectado ? 'bien' : 'mal' ?>">
        <b>Conexión</b><span><?= $conectado ? 'Conectada' : e(bd_motivo()) ?></span>
      </li>
      <li class="<?= $listo ? 'bien' : 'mal' ?>">
        <b>Tablas y procedimientos</b><span><?= $listo ? 'Listos' : ($conectado ? 'Faltan por crear' : 'Sin revisar') ?></span>
      </li>
    </ul>
    <?php if ($conectado && !$listo): ?>
      <p class="ayuda">Lo que respondió la base de datos: <?= e($detalle) ?></p>
      <form method="post"><button class="primary" type="submit">Preparar base de datos</button></form>
      <p class="ayuda">Crea las tablas cuenta_clientes y cuenta_abonos y los procedimientos sp_cuenta_… No borra datos.</p>
    <?php elseif ($listo): ?>
      <p class="ayuda">Todo está en orden. Ya puedes registrarte e ingresar con tu usuario.</p>
    <?php endif; ?>
    <p class="cambio"><a href="index.php">Volver a ingresar</a></p>
  </section>
<?php include __DIR__ . '/inc/pie.php'; ?>
