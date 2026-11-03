<?php
require 'inc/funciones.php';
exigir_sesion();

$aviso = tomar_aviso();
$formulario = tomar_formulario();

$destino = '';
$valor = '';

if (isset($formulario['destino'])) {
    $destino = $formulario['destino'];
}
if (isset($formulario['valor'])) {
    $valor = $formulario['valor'];
}

$cuenta = llamar('CALL sp_bf_consultar(?)', array($_SESSION['cliente_id']));
$saldo = 0;

if ($cuenta !== false && count($cuenta) > 0) {
    $saldo = (int) $cuenta[0]['saldo'];
}

$mi_id = (int) $_SESSION['cliente_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destino = '';
    if (isset($_POST['destino'])) {
        $destino = trim($_POST['destino']);
    }

    $valor = '';
    if (isset($_POST['valor'])) {
        $valor = trim($_POST['valor']);
    }

    $datos = array('destino' => $destino, 'valor' => $valor);

    if (!ctype_digit($destino) || (int) $destino < 1) {
        guardar_aviso('error', 'El ID del cliente debe ser un número entero mayor que cero.');
        guardar_formulario($datos);
        header('Location: enviar.php');
        exit;
    }

    if ((int) $destino == $mi_id) {
        guardar_aviso('error', 'No puedes enviarte dinero a ti mismo. Escribe el ID de otro cliente.');
        guardar_formulario($datos);
        header('Location: enviar.php');
        exit;
    }

    if (!ctype_digit($valor) || (int) $valor < 1) {
        guardar_aviso('error', 'Escribe un valor entero mayor que cero, solo con números.');
        guardar_formulario($datos);
        header('Location: enviar.php');
        exit;
    }

    if ((int) $valor > BF_MAXIMO) {
        guardar_aviso('error', 'El valor máximo por envío es ' . pesos(BF_MAXIMO) . '.');
        guardar_formulario($datos);
        header('Location: enviar.php');
        exit;
    }

    if ($conexion == null) {
        guardar_aviso('error', BF_SIN_CONEXION);
        guardar_formulario($datos);
        header('Location: enviar.php');
        exit;
    }

    $filas = llamar('CALL sp_bf_enviar(?,?,?)', array($mi_id, (int) $destino, (int) $valor));

    if ($filas === false || count($filas) == 0) {
        guardar_aviso('error', 'No pudimos registrar el envío. Intenta de nuevo en un momento.');
        guardar_formulario($datos);
        header('Location: enviar.php');
        exit;
    }

    $resultado = $filas[0]['resultado'];
    $saldo_bd = (int) $filas[0]['saldo'];

    if ($resultado == 'ok') {
        $texto = 'Enviaste ' . pesos($valor) . ' al cliente #' . (int) $destino . '. Tu nuevo saldo es ' . pesos($saldo_bd) . '.';

        guardar_aviso('ok', $texto);
        header('Location: cuenta.php');
        exit;
    }

    if ($resultado == 'no_existe') {
        $texto = 'No encontramos tu cuenta. Vuelve a ingresar.';
    } elseif ($resultado == 'destino_invalido') {
        $texto = 'Escribe el ID de otro cliente. No puedes enviarte dinero a ti mismo.';
    } elseif ($resultado == 'destino_no_existe') {
        $texto = 'No existe un cliente con el ID ' . (int) $destino . '. Revisa el número.';
    } elseif ($resultado == 'valor_invalido') {
        $texto = 'El valor debe ser un número entero mayor que cero.';
    } elseif ($resultado == 'saldo_cero') {
        $texto = 'No tienes saldo disponible. Consigna primero para poder enviar.';
    } elseif ($resultado == 'saldo_insuficiente') {
        $texto = 'Saldo insuficiente. Tienes ' . pesos($saldo_bd) . ' disponibles.';
    } else {
        $texto = 'No pudimos registrar el envío. Intenta de nuevo en un momento.';
    }

    guardar_aviso('error', $texto);
    guardar_formulario($datos);
    header('Location: enviar.php');
    exit;
}

$titulo = 'Enviar';
include 'inc/cabecera.php';
?>
<main class="contenido">
    <a class="volver" href="cuenta.php">← Volver a mi cuenta</a>

    <h1>Enviar dinero</h1>
    <p class="subtitulo">Manda plata de tu saldo a otro cliente de Aurora. Tu ID es <b>#<?= $mi_id ?></b> y tu saldo es <b><?= pesos($saldo) ?></b>.</p>

    <?php mostrar_aviso($aviso); ?>

    <?php if ($saldo == 0): ?>
        <div class="aviso aviso-aviso" role="status">
            <span class="aviso-icono" aria-hidden="true">i</span>
            <p>No tienes saldo disponible. Consigna para poder enviar.</p>
        </div>
    <?php endif; ?>

    <form class="panel" method="post" action="enviar.php">
        <label for="destino">ID del cliente que recibe</label>
        <input type="text" id="destino" name="destino" inputmode="numeric" pattern="[0-9]*" placeholder="Ejemplo: 2" value="<?= limpiar($destino) ?>" required aria-describedby="ayuda-destino">
        <p class="ayuda" id="ayuda-destino">Escribe el ID del cliente al que le quieres enviar. No puedes enviarte a ti mismo.</p>

        <label for="valor">Valor a enviar</label>
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

        <button type="submit" class="boton">Enviar</button>
    </form>
<?php include 'inc/pie.php'; ?>
