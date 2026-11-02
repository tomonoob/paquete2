<?php
require 'inc/funciones.php';

$mensaje = '';
$exito = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $conexion != null) {
    include 'inc/esquema.php';
    $exito = true;

    foreach ($sentencias as $sentencia) {
        if (!$conexion->query($sentencia)) {
            $mensaje = 'No se pudo preparar la base de datos: ' . $conexion->error;
            $exito = false;
            break;
        }
    }

    if ($exito) {
        $mensaje = 'Listo: las tablas y los procedimientos quedaron creados.';
    }
}

$motivo = '';
$listo = false;
$detalle = '';

if ($conexion == null) {
    if (getenv('DB_PASSWORD') == '') {
        $motivo = 'Falta la contraseña de la base de datos (variable DB_PASSWORD o archivo .env).';
    } else {
        $motivo = 'No se pudo conectar con el servidor de la base de datos.';
    }
} else {
    $pruebas = array(
        array('CALL sp_cuenta_buscar_cliente(?)', array('')),
        array('CALL sp_cuenta_listar_abonos(?)', array(0)),
        array('CALL sp_cuenta_consignar(?,?,?,?)', array(0, '2000-01-01', 'x', 0)),
        array('CALL sp_cuenta_retirar(?,?,?,?)', array(0, '2000-01-01', 'x', 0))
    );

    $listo = true;
    foreach ($pruebas as $prueba) {
        if (llamar($prueba[0], $prueba[1]) === false) {
            $listo = false;
            $detalle = $error_bd;
            break;
        }
    }
}

$titulo = 'Estado de la base de datos';
include 'inc/cabecera.php';
?>
<main>
    <div class="tarjeta">
        <h1>Base de datos</h1>
        <p class="subtitulo">Aquí ves si Banco puede usar tu base de datos.</p>

        <?php if ($mensaje != ''): ?>
            <p class="<?= $exito ? 'aviso aviso-ok' : 'error' ?>"><?= limpiar($mensaje) ?></p>
        <?php endif; ?>

        <ul class="estados">
            <?php if ($conexion != null): ?>
                <li class="bien"><b>Conexión</b><span>Conectada</span></li>
            <?php else: ?>
                <li class="mal"><b>Conexión</b><span><?= limpiar($motivo) ?></span></li>
            <?php endif; ?>

            <?php if ($listo): ?>
                <li class="bien"><b>Tablas y procedimientos</b><span>Listos</span></li>
            <?php elseif ($conexion != null): ?>
                <li class="mal"><b>Tablas y procedimientos</b><span>Faltan por crear</span></li>
            <?php else: ?>
                <li class="mal"><b>Tablas y procedimientos</b><span>Sin revisar</span></li>
            <?php endif; ?>
        </ul>

        <?php if ($conexion != null && !$listo): ?>
            <p class="ayuda">Lo que respondió la base de datos: <?= limpiar($detalle) ?></p>
            <form method="post" action="estado.php">
                <button type="submit" class="boton">Preparar base de datos</button>
            </form>
            <p class="ayuda">Crea las tablas cuenta_clientes y cuenta_abonos y los procedimientos sp_cuenta_… No borra datos.</p>
        <?php endif; ?>

        <?php if ($listo): ?>
            <p class="ayuda">Todo está en orden. Ya puedes registrarte e ingresar con tu usuario.</p>
        <?php endif; ?>

        <p class="enlace-centro"><a href="index.php">Volver a ingresar</a></p>
    </div>
<?php include 'inc/pie.php'; ?>
