<?php
require 'inc/funciones.php';
exigir_sesion();

$aviso = tomar_aviso();
$formulario = tomar_formulario();

$accion = 'agregar';
$valor = '';

if (isset($_GET['accion']) && $_GET['accion'] == 'sacar') {
    $accion = 'sacar';
}
if (isset($formulario['accion']) && $formulario['accion'] == 'sacar') {
    $accion = 'sacar';
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
    $accion = 'agregar';
    if (isset($_POST['accion']) && $_POST['accion'] == 'sacar') {
        $accion = 'sacar';
    }

    $valor = '';
    if (isset($_POST['valor'])) {
        $valor = trim($_POST['valor']);
    }

    $datos = array('accion' => $accion, 'valor' => $valor);
    $volver = 'ahorro.php?accion=' . $accion;

    if (!ctype_digit($valor) || (int) $valor < 1) {
        guardar_aviso('error', 'Escribe un valor entero mayor que cero, solo con números.');
        guardar_formulario($datos);
        header('Location: ' . $volver);
        exit;
    }

    if ((int) $valor > BF_MAXIMO) {
        guardar_aviso('error', 'El valor máximo por operación es ' . pesos(BF_MAXIMO) . '.');
        guardar_formulario($datos);
        header('Location: ' . $volver);
        exit;
    }

    if ($conexion == null) {
        guardar_aviso('error', BF_SIN_CONEXION);
        guardar_formulario($datos);
        header('Location: ' . $volver);
        exit;
    }

    $procedimiento = 'CALL sp_bf_ahorrar(?,?)';
    if ($accion == 'sacar') {
        $procedimiento = 'CALL sp_bf_sacar_ahorro(?,?)';
    }

    $filas = llamar($procedimiento, array((int) $_SESSION['cliente_id'], (int) $valor));

    if ($filas === false || count($filas) == 0) {
        guardar_aviso('error', 'No pudimos registrar la operación. Intenta de nuevo en un momento.');
        guardar_formulario($datos);
        header('Location: ' . $volver);
        exit;
    }

    $resultado = $filas[0]['resultado'];
    $saldo_bd = (int) $filas[0]['saldo'];
    $ahorro_bd = (int) $filas[0]['ahorro'];

    if ($resultado == 'ok') {
        if ($accion == 'sacar') {
            $texto = 'Sacaste ' . pesos($valor) . ' de tu ahorro. Tu nuevo saldo es ' . pesos($saldo_bd) . ' y tu ahorro es ' . pesos($ahorro_bd) . '.';
        } else {
            $texto = 'Ahorraste ' . pesos($valor) . '. Tu nuevo saldo es ' . pesos($saldo_bd) . ' y tu ahorro es ' . pesos($ahorro_bd) . '.';
        }

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
    } elseif ($resultado == 'ahorro_cero') {
        $texto = 'Todavía no tienes dinero ahorrado. Ahorra primero para poder sacar.';
    } elseif ($resultado == 'ahorro_insuficiente') {
        $texto = 'Ahorro insuficiente. Tienes ' . pesos($ahorro_bd) . ' ahorrados.';
    } else {
        $texto = 'No pudimos registrar la operación. Intenta de nuevo en un momento.';
    }

    guardar_aviso('error', $texto);
    guardar_formulario($datos);
    header('Location: ' . $volver);
    exit;
}

$titulo = 'Ahorro';
include 'inc/cabecera.php';
?>
<main class="contenido">
    <a class="volver" href="cuenta.php">← Volver a mi cuenta</a>

    <h1>Ahorro</h1>
    <p class="subtitulo">Tu saldo es <b><?= pesos($saldo) ?></b> y tu ahorro es <b><?= pesos($ahorro) ?></b>.</p>

    <?php mostrar_aviso($aviso); ?>

    <form class="panel" method="post" action="ahorro.php">
        <fieldset class="opciones">
            <legend>¿Qué quieres hacer?</legend>
            <label class="opcion">
                <input type="radio" name="accion" value="agregar" <?= $accion == 'agregar' ? 'checked' : '' ?>>
                <span>Agregar al ahorro</span>
            </label>
            <label class="opcion">
                <input type="radio" name="accion" value="sacar" <?= $accion == 'sacar' ? 'checked' : '' ?>>
                <span>Sacar del ahorro</span>
            </label>
        </fieldset>

        <label for="valor">Valor</label>
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

        <button type="submit" class="boton">Confirmar</button>
    </form>
<?php include 'inc/pie.php'; ?>
