<?php
require 'inc/funciones.php';

if (isset($_SESSION['modo'])) {
    header('Location: cuenta.php');
    exit;
}

$usuario = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];

    if ($usuario == '' || $clave == '') {
        $error = 'Escribe tu usuario y tu contraseña.';
    } elseif ($usuario == DEMO_USUARIO && $clave == DEMO_CLAVE) {
        iniciar_sesion('demo', 0, 'Camila', 'Torres');
    } else {
        $filas = llamar('CALL sp_cuenta_buscar_cliente(?)', array($usuario));

        if ($filas === false && $conexion == null) {
            $error = 'No hay conexión con la base de datos. Por ahora solo puedes entrar con la cuenta de prueba.';
        } elseif ($filas === false) {
            $error = 'La base de datos está conectada, pero faltan las tablas. Abre «Estado de la base de datos» para prepararla.';
        } elseif (count($filas) == 0 || !password_verify($clave, $filas[0]['pwd_hash'])) {
            $error = 'Usuario o contraseña incorrectos.';
        } else {
            iniciar_sesion('db', $filas[0]['id'], $filas[0]['nombres'], $filas[0]['apellidos']);
        }
    }
}

$titulo = 'Ingresar';
include 'inc/cabecera.php';
?>
<main>
    <div class="tarjeta">
        <h1>Ingresa a tu cuenta</h1>
        <p class="subtitulo">Consulta tu saldo y el historial de tus movimientos.</p>

        <form method="post" action="index.php">
            <label for="usuario">Nombre de usuario</label>
            <input type="text" id="usuario" name="usuario" value="<?= limpiar($usuario) ?>" required>

            <label for="clave">Contraseña</label>
            <div class="campo-clave">
                <input type="password" id="clave" name="clave" required>
                <button type="button" id="ver-clave" class="ver-clave">Mostrar</button>
            </div>

            <p class="error"><?= limpiar($error) ?></p>
            <button type="submit" class="boton">Ingresar</button>
        </form>

        <p class="enlace-centro">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>

        <div class="cuenta-prueba">
            <p><b>Cuenta de prueba</b></p>
            <p>Usuario: <code><?= DEMO_USUARIO ?></code> · Contraseña: <code><?= DEMO_CLAVE ?></code></p>
            <button type="button" id="usar-demo" class="boton-borde" data-usuario="<?= DEMO_USUARIO ?>" data-clave="<?= DEMO_CLAVE ?>">Usar esta cuenta</button>
        </div>

        <p class="enlace-centro"><a href="estado.php">Estado de la base de datos</a></p>
    </div>
<?php include 'inc/pie.php'; ?>
