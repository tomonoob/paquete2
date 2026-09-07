<?php
// Mantiene el comportamiento previo a PHP 8.1: los errores de mysqli
// se devuelven como valores (false / ->error) en vez de lanzar
// mysqli_sql_exception, que es el modo por defecto desde PHP 8.1 y
// rompería silenciosamente todos los bloques if/else de este proyecto.
mysqli_report(MYSQLI_REPORT_OFF);

$host = 'brayanrm-computacionmoviltarea.d.aivencloud.com';
$port = 16993;
$user = 'avnadmin';
$password = getenv('DB_PASSWORD');
$database = 'defaultdb';

if ($password === false || $password === '') {
    die("Error: la variable de entorno DB_PASSWORD no está definida.");
}

$conexion = mysqli_init();
mysqli_ssl_set($conexion, NULL, NULL, NULL, NULL, NULL);

if (!mysqli_real_connect($conexion, $host, $user, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Error de conexión a Aiven: " . mysqli_connect_error());
}

$conexion->set_charset("utf8mb4");
$conexion->query("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
?>