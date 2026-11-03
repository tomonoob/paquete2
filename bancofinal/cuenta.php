<?php
require 'inc/funciones.php';
exigir_sesion();

$aviso = tomar_aviso();

$cuenta = llamar('CALL sp_bf_consultar(?)', array($_SESSION['cliente_id']));
$historial = false;

if ($cuenta !== false) {
    $historial = llamar('CALL sp_bf_historial(?)', array($_SESSION['cliente_id']));
}

$saldo = 0;
$ahorro = 0;
$hay_cuenta = false;

if ($cuenta !== false && count($cuenta) > 0) {
    $saldo = (int) $cuenta[0]['saldo'];
    $ahorro = (int) $cuenta[0]['ahorro'];
    $hay_cuenta = true;
}

$cantidad = 0;
if ($historial !== false) {
    $cantidad = count($historial);
}

$inicial = mb_substr($_SESSION['primer_nombre'], 0, 1, 'UTF-8');
$inicial = mb_strtoupper($inicial, 'UTF-8');
if ($inicial == '') {
    $inicial = 'A';
}

$titulo = 'Mi cuenta';
include 'inc/cabecera.php';
?>
<main class="contenido">
    <section class="saludo">
        <div>
            <p class="saludo-fecha"><?= fecha_hoy() ?></p>
            <h1>Hola, <?= limpiar($_SESSION['primer_nombre']) ?></h1>
            <p class="saludo-id">Cliente #<?= (int) $_SESSION['cliente_id'] ?></p>
        </div>
        <span class="avatar" aria-hidden="true"><?= limpiar($inicial) ?></span>
    </section>

    <?php mostrar_aviso($aviso); ?>

    <?php if ($cuenta === false): ?>
        <div class="aviso aviso-error" role="alert">
            <span class="aviso-icono" aria-hidden="true">!</span>
            <p><?= limpiar(BF_SIN_CONEXION) ?></p>
        </div>
        <p class="enlace-pie"><a href="estado.php">Revisar el estado de la base de datos</a></p>
    <?php elseif (!$hay_cuenta): ?>
        <div class="aviso aviso-error" role="alert">
            <span class="aviso-icono" aria-hidden="true">!</span>
            <p>No encontramos tu cuenta. Vuelve a ingresar.</p>
        </div>
        <p class="enlace-pie"><a href="logout.php">Volver a ingresar</a></p>
    <?php else: ?>

        <section class="tarjetas" aria-label="Resumen de tu cuenta">
            <article class="tarjeta-saldo">
                <p class="etiqueta">Saldo disponible</p>
                <p class="monto-grande" id="saldo" data-saldo="<?= $saldo ?>"><?= pesos($saldo) ?></p>
                <p class="nota">Para consignar, retirar, ahorrar o enviar.</p>
            </article>

            <article class="tarjeta-ahorro">
                <p class="etiqueta">Tu ahorro</p>
                <p class="monto-medio"><?= pesos($ahorro) ?></p>
                <p class="nota">Guardado para tus metas.</p>
            </article>
        </section>

        <?php if ($saldo == 0): ?>
            <div class="aviso aviso-aviso" role="status">
                <span class="aviso-icono" aria-hidden="true">i</span>
                <p>No tienes saldo disponible. Consigna para poder ahorrar.</p>
            </div>
        <?php endif; ?>

        <nav class="acciones" aria-label="Acciones de tu cuenta">
            <a class="accion" href="ahorro.php">
                <span class="accion-icono icono-ahorro"><?= icono_movimiento('ahorro') ?></span>
                <span>Ahorro</span>
            </a>
            <a class="accion" href="enviar.php">
                <span class="accion-icono icono-envio"><?= icono_movimiento('envio') ?></span>
                <span>Enviar</span>
            </a>
            <a class="accion" href="movimiento.php?tipo=consignar">
                <span class="accion-icono icono-consignacion"><?= icono_movimiento('consignacion') ?></span>
                <span>Consignar</span>
            </a>
            <a class="accion" href="movimiento.php?tipo=retirar">
                <span class="accion-icono icono-retiro"><?= icono_movimiento('retiro') ?></span>
                <span>Retirar</span>
            </a>
        </nav>

        <section class="historial" aria-labelledby="titulo-historial">
            <div class="titulo-fila">
                <h2 id="titulo-historial">Movimientos</h2>
                <?php if ($cantidad > 0): ?>
                    <span class="contador"><?= $cantidad ?></span>
                <?php endif; ?>
            </div>

            <?php if ($historial === false): ?>
                <div class="aviso aviso-error" role="alert">
                    <span class="aviso-icono" aria-hidden="true">!</span>
                    <p>No pudimos cargar tus movimientos. Recarga la página en un momento.</p>
                </div>
            <?php elseif ($cantidad == 0): ?>
                <div class="vacio">
                    <span class="vacio-icono" aria-hidden="true"><?= icono_movimiento('consignacion') ?></span>
                    <b>Aún no hay movimientos</b>
                    <p>Cuando consignes, ahorres o retires, lo verás aquí.</p>
                </div>
            <?php else: ?>
                <ul class="lista">
                    <?php foreach ($historial as $movimiento): ?>
                        <?php
                        $tipo = $movimiento['tipo'];
                        $clase = 'mov-ahorro';
                        $signo = '−';

                        if ($tipo == 'consignacion') {
                            $clase = 'mov-consignacion';
                            $signo = '+';
                        } elseif ($tipo == 'retiro') {
                            $clase = 'mov-retiro';
                        } elseif ($tipo == 'ahorro_retiro') {
                            $clase = 'mov-ahorro-retiro';
                            $signo = '+';
                        } elseif ($tipo == 'envio') {
                            $clase = 'mov-envio';
                        } elseif ($tipo == 'recibido') {
                            $clase = 'mov-recibido';
                            $signo = '+';
                        }
                        ?>
                        <li>
                            <span class="mov-icono <?= $clase ?>"><?= icono_movimiento($tipo) ?></span>
                            <span class="mov-datos">
                                <b><?= nombre_movimiento($tipo) ?></b>
                                <small><?= fecha_hora_corta($movimiento['fecha']) ?></small>
                            </span>
                            <span class="mov-valor <?= $clase ?>"><?= $signo ?> <?= pesos($movimiento['valor']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

    <?php endif; ?>
<?php include 'inc/pie.php'; ?>
