<?php
require 'inc/funciones.php';

if (hay_sesion()) {
    header('Location: cuenta.php');
    exit;
}

$aviso = tomar_aviso();
$formulario = tomar_formulario();
$usuario = '';

if (isset($formulario['usuario'])) {
    $usuario = $formulario['usuario'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['usuario'])) {
        $usuario = trim($_POST['usuario']);
    }

    $clave = '';
    if (isset($_POST['clave'])) {
        $clave = $_POST['clave'];
    }

    if ($usuario == '' || $clave == '') {
        guardar_aviso('error', 'Escribe tu usuario y tu contraseña.');
        guardar_formulario(array('usuario' => $usuario));
        header('Location: index.php');
        exit;
    }

    $filas = llamar('CALL sp_bf_buscar_usuario(?)', array($usuario));

    if ($filas === false) {
        guardar_aviso('error', BF_SIN_CONEXION);
        guardar_formulario(array('usuario' => $usuario));
        header('Location: index.php');
        exit;
    }

    if (count($filas) == 0 || !password_verify($clave, $filas[0]['pwd_hash'])) {
        guardar_aviso('error', 'Usuario o contraseña incorrectos. Revisa los datos e intenta otra vez.');
        guardar_formulario(array('usuario' => $usuario));
        header('Location: index.php');
        exit;
    }

    iniciar_sesion($filas[0]['id'], $filas[0]['nombres'], $filas[0]['apellidos']);
}

$titulo = 'Ingresar';
include 'inc/cabecera.php';
?>
<main class="contenido">
    <section class="portada">
        <img class="portada-logo" src="assets/img/logo-aurora-512.png" alt="Logo de Aurora" width="104" height="104">
        <p class="eyebrow">Banco digital</p>
        <h1>Tu plata, clara y a mano.</h1>
        <p class="portada-texto">Consulta tu saldo, ahorra y mueve tu dinero en segundos.</p>
    </section>

    <?php mostrar_aviso($aviso); ?>

    <form class="panel" method="post" action="index.php">
        <h2>Ingresa a tu cuenta</h2>

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" value="<?= limpiar($usuario) ?>" autocomplete="username" autocapitalize="none" spellcheck="false" required>

        <label for="clave">Contraseña</label>
        <div class="campo-clave">
            <input type="password" id="clave" name="clave" autocomplete="current-password" required>
            <button type="button" class="ver-clave" data-campo="clave">Mostrar</button>
        </div>

        <button type="submit" class="boton">Ingresar</button>

        <p class="cambio">¿No tienes cuenta? <a href="registro.php">Crea una</a></p>
    </form>

    <section class="panel panel-prueba">
        <div class="prueba-titulo">
            <span class="punto-aurora" aria-hidden="true"></span>
            <h2>Cuenta de prueba</h2>
        </div>
        <p>Entra con estos datos para conocer la app.</p>
        <div class="prueba-datos">
            <span>Usuario <b><?= limpiar(BF_PRUEBA_USUARIO) ?></b></span>
            <span>Contraseña <b><?= limpiar(BF_PRUEBA_CLAVE) ?></b></span>
        </div>
        <button type="button" id="usar-prueba" class="boton boton-suave" data-usuario="<?= limpiar(BF_PRUEBA_USUARIO) ?>" data-clave="<?= limpiar(BF_PRUEBA_CLAVE) ?>">Usar esta cuenta</button>
    </section>

    <p class="enlace-pie"><a href="estado.php">Estado de la base de datos</a></p>
<?php include 'inc/pie.php'; ?>
