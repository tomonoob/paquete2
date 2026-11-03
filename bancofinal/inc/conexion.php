<?php
// Los errores de mysqli se devuelven como false o ->error, no como excepciones.
mysqli_report(MYSQLI_REPORT_OFF);

$archivoEnv = __DIR__ . '/../../.env';
if (getenv('DB_PASSWORD') == '' && file_exists($archivoEnv)) {
    foreach (file($archivoEnv) as $linea) {
        if (substr($linea, 0, 12) == 'DB_PASSWORD=') {
            $valor = trim(substr($linea, 12));
            putenv('DB_PASSWORD=' . str_replace(array('"', "'"), '', $valor));
        }
    }
}

$host = 'brayanrm-computacionmoviltarea.d.aivencloud.com';
$port = 16993;
$usuario_bd = 'avnadmin';
$nombre_bd = 'defaultdb';
$clave_bd = getenv('DB_PASSWORD');

$conexion = null;
$error_bd = '';

if ($clave_bd !== false && $clave_bd != '') {
    $conexion = mysqli_init();

    if ($conexion) {
        $conexion->options(MYSQLI_OPT_CONNECT_TIMEOUT, 8);
        mysqli_ssl_set($conexion, null, null, null, null, null);

        $conectado = @mysqli_real_connect($conexion, $host, $usuario_bd, $clave_bd, $nombre_bd, $port, null, MYSQLI_CLIENT_SSL);

        if (!$conectado) {
            $conexion = null;
        } else {
            $conexion->set_charset('utf8mb4');
            $conexion->query("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
            $conexion->query("SET time_zone = '-05:00'");
        }
    }
}

function llamar($sql, $datos)
{
    global $conexion, $error_bd;

    if ($conexion == null) {
        $error_bd = 'Sin conexión con la base de datos.';
        return false;
    }

    $consulta = $conexion->prepare($sql);
    if (!$consulta) {
        $error_bd = $conexion->error;
        return false;
    }

    if (!$consulta->execute($datos)) {
        $error_bd = $consulta->error;
        $consulta->close();
        return false;
    }

    $filas = array();
    $resultado = $consulta->get_result();
    if ($resultado) {
        $filas = $resultado->fetch_all(MYSQLI_ASSOC);
    }
    $consulta->close();

    while ($conexion->more_results() && $conexion->next_result()) {
    }

    return $filas;
}
