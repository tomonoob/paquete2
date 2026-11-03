<?php
session_set_cookie_params(array('httponly' => true, 'samesite' => 'Lax'));
session_start();
date_default_timezone_set('America/Bogota');

require __DIR__ . '/conexion.php';

define('BF_PRUEBA_USUARIO', 'prueba1');
define('BF_PRUEBA_CLAVE', '123456');
define('BF_MAXIMO', 1000000000);
define('BF_SIN_CONEXION', 'No hay conexión con la base de datos. Intenta de nuevo en un momento.');

function limpiar($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function pesos($numero)
{
    return '$ ' . number_format((float) $numero, 0, ',', '.');
}

function fecha_hoy()
{
    $dias = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
    $meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

    return ucfirst($dias[date('w')]) . ', ' . date('j') . ' de ' . $meses[date('n') - 1];
}

function fecha_hora_corta($fecha)
{
    $meses = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
    $tiempo = strtotime($fecha);

    return date('j', $tiempo) . ' ' . $meses[date('n', $tiempo) - 1] . ' ' . date('Y', $tiempo) . ' · ' . date('g:i a', $tiempo);
}

function hay_sesion()
{
    return isset($_SESSION['cliente_id']);
}

function exigir_sesion()
{
    if (!hay_sesion()) {
        header('Location: index.php');
        exit;
    }
}

function iniciar_sesion($id, $nombres, $apellidos)
{
    session_regenerate_id(true);

    $_SESSION['cliente_id'] = (int) $id;
    $_SESSION['primer_nombre'] = $nombres;
    $_SESSION['nombre_completo'] = $nombres . ' ' . $apellidos;

    header('Location: cuenta.php');
    exit;
}

function guardar_aviso($tipo, $texto)
{
    $_SESSION['bf_aviso'] = array('tipo' => $tipo, 'texto' => $texto);
}

function tomar_aviso()
{
    if (!isset($_SESSION['bf_aviso'])) {
        return null;
    }

    $aviso = $_SESSION['bf_aviso'];
    unset($_SESSION['bf_aviso']);

    return $aviso;
}

function guardar_formulario($datos)
{
    $_SESSION['bf_formulario'] = $datos;
}

function tomar_formulario()
{
    if (!isset($_SESSION['bf_formulario'])) {
        return array();
    }

    $datos = $_SESSION['bf_formulario'];
    unset($_SESSION['bf_formulario']);

    return $datos;
}

function mostrar_aviso($aviso)
{
    if ($aviso == null) {
        return;
    }

    $clase = 'aviso-aviso';
    $rol = 'status';
    $simbolo = 'i';

    if ($aviso['tipo'] == 'ok') {
        $clase = 'aviso-ok';
        $simbolo = '✓';
    } elseif ($aviso['tipo'] == 'error') {
        $clase = 'aviso-error';
        $rol = 'alert';
        $simbolo = '!';
    }

    echo '<div class="aviso ' . $clase . '" role="' . $rol . '">';
    echo '<span class="aviso-icono" aria-hidden="true">' . $simbolo . '</span>';
    echo '<p>' . limpiar($aviso['texto']) . '</p>';
    echo '</div>';
}

function nombre_movimiento($tipo)
{
    if ($tipo == 'ahorro') {
        return 'Ahorro';
    }
    if ($tipo == 'retiro') {
        return 'Retiro';
    }
    if ($tipo == 'ahorro_retiro') {
        return 'Sacaste del ahorro';
    }
    if ($tipo == 'envio') {
        return 'Envío';
    }
    if ($tipo == 'recibido') {
        return 'Recibido';
    }

    return 'Consignación';
}

function icono_movimiento($tipo)
{
    if ($tipo == 'ahorro') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 3.4l2.6 5.2 5.7.8-4.1 4 1 5.7-5.2-2.7-5.2 2.7 1-5.7-4.1-4 5.7-.8z"/></svg>';
    }

    if ($tipo == 'retiro') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V8"/><path d="M7.5 12.5L12 8l4.5 4.5"/><path d="M5 4h14"/></svg>';
    }

    if ($tipo == 'ahorro_retiro') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.4l2.6 5.2 5.7.8-4.1 4 1 5.7-5.2-2.7-5.2 2.7 1-5.7-4.1-4 5.7-.8z"/></svg>';
    }

    if ($tipo == 'envio') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3L10.5 13.5"/><path d="M21 3l-6.5 18-4-8-8-4z"/></svg>';
    }

    if ($tipo == 'recibido') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h4l1.5 2.5h5L16 12h4"/><path d="M6 4h12l2 8v6H4v-6z"/></svg>';
    }

    return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v12"/><path d="M7.5 11.5L12 16l4.5-4.5"/><path d="M5 20h14"/></svg>';
}
