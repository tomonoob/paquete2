<?php
require 'inc/funciones.php';
exigir_sesion();

$aviso = tomar_aviso();
$formulario = tomar_formulario();

$id = $_SESSION['cliente_id'];
$valor = '';

if (isset($formulario['cliente_id'])) {
    $id = $formulario['cliente_id'];
}
if (isset($formulario['valor'])) {
    $valor = $formulario['valor'];
}

$cuenta = llamar('CALL sp_bf_consultar(?)', array($_SESSION['cliente_id']));
$saldo = 0;
$ahorro = 0;

if ($cuenta !== false && count($cuenta) > 0) {
    $saldo = (int) $cuenta[0]['saldo'];
    $ahorro = (int) $cuenta[0]['ahorro'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = '';
    if (isset($_POST['cliente_id'])) {
        $id = trim($_POST['cliente_id']);
    }

    $valor = '';
    if (isset($_POST['valor'])) {
        $valor = trim($_POST['valor']);
    }

    $datos = array('cliente_id' => $id, 'valor' => $valor);

    if (!ctype_digit($id) || (int) $id < 1) {
        guardar_aviso('error', 'El ID del cliente debe ser un número entero mayor que cero.');
        guardar_formulario($datos);
        header('Location: ahorrar.php');
        exit;
    }

    if ((int) $id != (int) $_SESSION['cliente_id']) {
        guardar_aviso('error', 'Solo puedes ahorrar desde tu propia cuenta. Tu ID es ' . (int) $_SESSION['cliente_id'] . '.');
        guardar_formulario($datos);
        header('Location: ahorrar.php');
        exit;
    }

    if (!ctype_digit($valor) || (int) $valor < 1) {
        guardar_aviso('error', 'Escribe un valor entero mayor que cero, solo con números.');
        guardar_formulario($datos);
        header('Location: ahorrar.php');
        exit;
    }

    if ((int) $valor > BF_MAXIMO) {
        guardar_aviso('error', 'El valor máximo por operación es ' . pesos(BF_MAXIMO) . '.');
        guardar_formulario($datos);
        header('Location: ahorrar.php');
        exit;
    }

    if ($conexion == null) {
        guardar_aviso('error', BF_SIN_CONEXION);
        guardar_formulario($datos);
        header('Location: ahorrar.php');
        exit;
    }

    $filas = llamar('CALL sp_bf_ahorrar(?,?)', array((int) $_SESSION['cliente_id'], (int) $valor));

    if ($filas === false || count($filas) == 0) {
        guardar_aviso('error', 'No pudimos registrar el ahorro. Intenta de nuevo en un momento.');
        guardar_formulario($datos);
        header('Location: ahorrar.php');
        exit;
    }

    $resultado = $filas[0]['resultado'];
    $saldo_bd = (int) $filas[0]['saldo'];
    $ahorro_bd = (int) $filas[0]['ahorro'];

    if ($resultado == 'ok') {
        $texto = 'Ahorraste ' . pesos($valor) . '. Tu nuevo saldo es ' . pesos($saldo_bd) . ' y tu ahorro es ' . pesos($ahorro_bd) . '.';

        guardar_aviso('ok', $texto);
        header('Location: cuenta.php');
        exit;
    }

    if ($resultado == 'no_existe') {
        $texto = 'No encontramos tu cuenta. Vuelve a ingresar.';
    } elseif ($resultado == 'valor_invalido') {
        $texto = 'El valor debe ser un número entero mayor que cero.';
    } elseif ($resultado == 'saldo_cero') {
        $texto = 'No tienes saldo disponible. Consigna primero para poder ahorrar.';
    } elseif ($resultado == 'saldo_insuficiente') {
        $texto = 'Saldo insuficiente. Tienes ' . pesos($saldo_bd) . ' disponibles.';
    } else {
        $texto = 'No pudimos registrar el ahorro. Intenta de nuevo en un momento.';
    }

    guardar_aviso('error', $texto);
    guardar_formulario($datos);
    header('Location: ahorrar.php');
    exit;
}

$titulo = 'Ahorrar';
include 'inc/cabecera.php';
?>
<main class="contenido">
    <a class="volver" href="cuenta.php">← Volver a mi cuenta</a>

    <h1>Ahorrar</h1>
    <p class="subtitulo">Pasa plata de tu saldo a tu ahorro. Tu saldo es <b><?= pesos($saldo) ?></b> y tu ahorro es <b><?= pesos($ahorro) ?></b>.</p>

    <?php mostrar_aviso($aviso); ?>

    <form class="panel" method="post" action="ahorrar.php">
        <label for="cliente_id">ID del cliente</label>
        <input type="text" id="cliente_id" name="cliente_id" inputmode="numeric" pattern="[0-9]*" value="<?= limpiar($id) ?>" required aria-describedby="ayuda-id">
        <p class="ayuda" id="ayuda-id">Viene con tu ID. Solo puedes ahorrar desde tu propia cuenta.</p>

        <label for="valor">Valor a ahorrar</label>
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

        <button type="submit" class="boton">Ahorrar</button>
    </form>
<?php include 'inc/pie.php'; ?>
