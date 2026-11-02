<?php
require 'inc/funciones.php';

if (isset($_SESSION['modo'])) {
    header('Location: cuenta.php');
    exit;
}

$nombres = '';
$apellidos = '';
$usuario = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];
    $repite = $_POST['repite'];

    if ($nombres == '' || $apellidos == '' || $usuario == '' || $clave == '') {
        $error = 'Completa todos los campos.';
    } elseif (strlen($usuario) < 3 || strlen($usuario) > 40) {
        $error = 'El usuario debe tener entre 3 y 40 caracteres.';
    } elseif (strtolower($usuario) == DEMO_USUARIO) {
        $error = 'Ese nombre de usuario no está disponible.';
    } elseif (strlen($clave) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($clave != $repite) {
        $error = 'Las contraseñas no coinciden.';
    } elseif ($conexion == null) {
        $error = 'El registro necesita la base de datos y ahora no está disponible. Entra con la cuenta de prueba.';
    } else {
        $clave_cifrada = password_hash($clave, PASSWORD_DEFAULT);
        $filas = llamar('CALL sp_cuenta_registrar_cliente(?,?,?,?)', array($usuario, $clave_cifrada, $nombres, $apellidos));

        if ($filas === false) {
            $error = 'No pudimos guardar el registro. Abre «Estado de la base de datos» desde la pantalla de ingreso.';
        } elseif ($filas[0]['id'] == 0) {
            $error = 'Ese nombre de usuario ya está en uso.';
        } else {
            iniciar_sesion('db', $filas[0]['id'], $nombres, $apellidos);
        }
    }
}

$titulo = 'Crear cuenta';
include 'inc/cabecera.php';
?>
<main>
    <div class="tarjeta">
        <h1>Crea tu cuenta</h1>
        <p class="subtitulo">Regístrate para consultar tu saldo y tus movimientos.</p>

        <form method="post" action="registro.php">
            <label for="nombres">Nombres</label>
            <input type="text" id="nombres" name="nombres" maxlength="80" value="<?= limpiar($nombres) ?>" required>

            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" maxlength="80" value="<?= limpiar($apellidos) ?>" required>

            <label for="usuario">Nombre de usuario</label>
            <input type="text" id="usuario" name="usuario" maxlength="40" value="<?= limpiar($usuario) ?>" required>

            <label for="clave">Contraseña</label>
            <div class="campo-clave">
                <input type="password" id="clave" name="clave" required>
                <button type="button" id="ver-clave" class="ver-clave">Mostrar</button>
            </div>
            <p class="ayuda">Mínimo 6 caracteres.</p>

            <label for="repite">Repite la contraseña</label>
            <input type="password" id="repite" name="repite" required>

            <p class="error"><?= limpiar($error) ?></p>
            <button type="submit" class="boton">Crear cuenta</button>
        </form>

        <p class="enlace-centro">¿Ya tienes cuenta? <a href="index.php">Ingresa</a></p>
    </div>
<?php include 'inc/pie.php'; ?>
