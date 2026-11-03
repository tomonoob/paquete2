<?php
require 'inc/funciones.php';

$_SESSION = array();

$parametros = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);

session_destroy();

header('Location: index.php');
exit;
