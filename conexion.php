<?php
$host = 'brayanrm-computacionmoviltarea.d.aivencloud.com';
$port = 16993;
$user = 'avnadmin';
$password = getenv('DB_PASSWORD') ?: 'TU_CONTRASEÑA_DE_AIVEN';
$database = 'defaultdb';

$conexion = mysqli_init();
mysqli_ssl_set($conexion, NULL, NULL, NULL, NULL, NULL);

if (!mysqli_real_connect($conexion, $host, $user, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Error de conexión a Aiven: " . mysqli_connect_error());
}

// Configurar charset y cotejamiento de la conexión
mysqli_set_charset($conexion, "utf8mb4");
$conexion->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");
?>