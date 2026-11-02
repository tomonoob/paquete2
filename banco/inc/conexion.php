<?php
define('CONEXION_SILENCIOSA', true);
$conexion = null;
@include __DIR__ . '/../../comun/conexion.php';

$error_bd = '';

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
