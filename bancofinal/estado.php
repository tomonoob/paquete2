<?php
require 'inc/funciones.php';

$motivo = '';
$conectado = ($conexion != null);
$listo = false;

if (!$conectado) {
    if (getenv('DB_PASSWORD') == '') {
        $motivo = 'La aplicación no tiene configurada la contraseña de la base de datos.';
    } else {
        $motivo = 'No se pudo conectar con el servidor. Puede estar apagado o sin red.';
    }
} else {
    $pruebas = array(
        array('CALL sp_bf_buscar_usuario(?)', array('')),
        array('CALL sp_bf_consultar(?)', array(0)),
        array('CALL sp_bf_ahorrar(?,?)', array(0, 1)),
        array('CALL sp_bf_consignar(?,?)', array(0, 1)),
        array('CALL sp_bf_retirar(?,?)', array(0, 1)),
        array('CALL sp_bf_historial(?)', array(0))
    );

    $listo = true;
    foreach ($pruebas as $prueba) {
        if (llamar($prueba[0], $prueba[1]) === false) {
            $listo = false;
            break;
        }
    }
}

$volver = 'index.php';
$texto_volver = 'Volver a ingresar';

if (hay_sesion()) {
    $volver = 'cuenta.php';
    $texto_volver = 'Volver a mi cuenta';
}

$titulo = 'Estado de la base de datos';
include 'inc/cabecera.php';
?>
<main class="contenido">
    <a class="volver" href="<?= $volver ?>">← <?= $texto_volver ?></a>

    <h1>Estado de la base de datos</h1>
    <p class="subtitulo">Así está la conexión de Aurora en este momento.</p>

    <ul class="estados">
        <?php if ($conectado): ?>
            <li class="bien">
                <span>Conexión</span>
                <span class="estado-valor">Conectada</span>
            </li>
        <?php else: ?>
            <li class="mal">
                <span>Conexión</span>
                <span class="estado-valor">Sin conexión</span>
            </li>
        <?php endif; ?>

        <?php if ($listo): ?>
            <li class="bien">
                <span>Tablas y procedimientos</span>
                <span class="estado-valor">Responden</span>
            </li>
        <?php elseif ($conectado): ?>
            <li class="mal">
                <span>Tablas y procedimientos</span>
                <span class="estado-valor">Faltan o no responden</span>
            </li>
        <?php else: ?>
            <li class="mal">
                <span>Tablas y procedimientos</span>
                <span class="estado-valor">Sin revisar</span>
            </li>
        <?php endif; ?>
    </ul>

    <?php if (!$conectado): ?>
        <div class="aviso aviso-error" role="alert">
            <span class="aviso-icono" aria-hidden="true">!</span>
            <p><?= limpiar($motivo) ?></p>
        </div>
        <p class="ayuda">Revisa la conexión a internet e intenta de nuevo en un momento.</p>
    <?php elseif (!$listo): ?>
        <div class="aviso aviso-aviso" role="status">
            <span class="aviso-icono" aria-hidden="true">i</span>
            <p>La conexión funciona, pero faltan las tablas o los procedimientos.</p>
        </div>
        <p class="ayuda">Ejecuta el archivo <b>bancofinal/sql/bancofinal.sql</b> completo en MySQL Workbench.</p>
    <?php else: ?>
        <div class="aviso aviso-ok" role="status">
            <span class="aviso-icono" aria-hidden="true">✓</span>
            <p>Todo está listo. Ya puedes ingresar o crear tu cuenta.</p>
        </div>
    <?php endif; ?>
<?php include 'inc/pie.php'; ?>
