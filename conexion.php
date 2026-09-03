<?php
$host = 'brayanrm-computacionmoviltarea.d.aivencloud.com';
$port = 16993;
$user = 'avnadmin';
$password = 'AVNS_Wse0HyHz3n_dDniY1Sr';
$database = 'defaultdb';

// Inicializar MySQLi
$conexion = mysqli_init();

mysqli_ssl_set($conexion, NULL, NULL, NULL, NULL, NULL);

// Conectar
if (!mysqli_real_connect($conexion, $host, $user, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Error de conexión a Aiven: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
?>