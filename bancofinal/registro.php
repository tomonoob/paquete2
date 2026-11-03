<?php
require 'inc/funciones.php';

if (hay_sesion()) {
    header('Location: cuenta.php');
    exit;
}

$aviso = tomar_aviso();
$formulario = tomar_formulario();

$nombres = '';
$apellidos = '';
$usuario = '';

if (isset($formulario['nombres'])) {
    $nombres = $formulario['nombres'];
}
if (isset($formulario['apellidos'])) {
    $apellidos = $formulario['apellidos'];
}
if (isset($formulario['usuario'])) {
    $usuario = $formulario['usuario'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nombres'])) {
        $nombres = trim($_POST['nombres']);
    }
    if (isset($_POST['apellidos'])) {
        $apellidos = trim($_POST['apellidos']);
    }
    if (isset($_POST['usuario'])) {
        $usuario = trim($_POST['usuario']);
    }

    $clave = '';
    if (isset($_POST['clave'])) {
        $clave = $_POST['clave'];
    }

    $repite = '';
    if (isset($_POST['repite'])) {
        $repite = $_POST['repite'];
    }

    $datos = array('nombres' => $nombres, 'apellidos' => $apellidos, 'usuario' => $usuario);

    if ($nombres == '' || $apellidos == '' || $usuario == '' || $clave == '') {
        guardar_aviso('error', 'Completa todos los campos.');
        guardar_formulario($datos);
        header('Location: registro.php');
        exit;
    }

    if (strlen($nombres) > 80 || strlen($apellidos) > 80) {
        guardar_aviso('error', 'Los nombres y apellidos deben tener máximo 80 caracteres.');
        guardar_formulario($datos);
        header('Location: registro.php');
        exit;
    }

    if (strlen($usuario) < 3 || strlen($usuario) > 40) {
        guardar_aviso('error', 'El usuario debe tener entre 3 y 40 caracteres.');
        guardar_formulario($datos);
        header('Location: registro.php');
        exit;
    }

    if (strlen($clave) < 6) {
        guardar_aviso('error', 'La contraseña debe tener al menos 6 caracteres.');
        guardar_formulario($datos);
        header('Location: registro.php');
        exit;
    }

    if ($clave != $repite) {
        guardar_aviso('error', 'Las contraseñas no coinciden.');
        guardar_formulario($datos);
        header('Location: registro.php');
        exit;
    }

    if ($conexion == null) {
        guardar_aviso('error', BF_SIN_CONEXION);
        guardar_formulario($datos);
        header('Location: registro.php');
        exit;
    }

    $clave_cifrada = password_hash($clave, PASSWORD_DEFAULT);
    $filas = llamar('CALL sp_bf_registrar(?,?,?,?)', array($usuario, $clave_cifrada, $nombres, $apellidos));

    if ($filas === false || count($filas) == 0) {
        guardar_aviso('error', 'No pudimos crear tu cuenta. Intenta de nuevo en un momento.');
        guardar_formulario($datos);
        header('Location: registro.php');
        exit;
    }

    if ($filas[0]['id'] == 0) {
        guardar_aviso('error', 'Ese nombre de usuario ya está en uso. Prueba con otro.');
        guardar_formulario($datos);
        header('Location: registro.php');
        exit;
    }

    iniciar_sesion($filas[0]['id'], $nombres, $apellidos);
}

$titulo = 'Crear cuenta';
include 'inc/cabecera.php';
?>
<main class="contenido">
    <a class="volver" href="index.php">← Ya tengo cuenta</a>

    <h1>Crea tu cuenta</h1>
    <p class="subtitulo">Solo necesitas tus datos para empezar.</p>

    <?php mostrar_aviso($aviso); ?>

    <form class="panel" method="post" action="registro.php">
        <label for="nombres">Nombres</label>
        <input type="text" id="nombres" name="nombres" maxlength="80" value="<?= limpiar($nombres) ?>" autocomplete="given-name" autocapitalize="words" required>

        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" maxlength="80" value="<?= limpiar($apellidos) ?>" autocomplete="family-name" autocapitalize="words" required>

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" maxlength="40" value="<?= limpiar($usuario) ?>" autocomplete="username" autocapitalize="none" spellcheck="false" required aria-describedby="ayuda-usuario">
        <p class="ayuda" id="ayuda-usuario">Entre 3 y 40 caracteres.</p>

        <label for="clave">Contraseña</label>
        <div class="campo-clave">
            <input type="password" id="clave" name="clave" autocomplete="new-password" required aria-describedby="ayuda-clave">
            <button type="button" class="ver-clave" data-campo="clave">Mostrar</button>
        </div>
        <p class="ayuda" id="ayuda-clave">Mínimo 6 caracteres.</p>

        <label for="repite">Repite la contraseña</label>
        <div class="campo-clave">
            <input type="password" id="repite" name="repite" autocomplete="new-password" required>
            <button type="button" class="ver-clave" data-campo="repite">Mostrar</button>
        </div>

        <button type="submit" class="boton">Crear cuenta</button>

        <p class="cambio">¿Ya tienes cuenta? <a href="index.php">Ingresa</a></p>
    </form>
<?php include 'inc/pie.php'; ?>
