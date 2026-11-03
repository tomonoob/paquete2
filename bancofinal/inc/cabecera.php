<?php
$enlaceMarca = 'index.php';
if (hay_sesion()) {
    $enlaceMarca = 'cuenta.php';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#132A4A">
<title><?= limpiar($titulo) ?> · Banco Aurora</title>
<link rel="icon" type="image/png" href="assets/img/logo-512.png">
<link rel="apple-touch-icon" href="assets/img/logo-512.png">
<link rel="stylesheet" href="assets/css/estilos.css">
<script>
var mostrarCarga = true;
try {
    mostrarCarga = !sessionStorage.getItem('bf_carga');
} catch (error) {
    mostrarCarga = true;
}
if (mostrarCarga) {
    document.documentElement.classList.add('con-carga');
}
</script>
<noscript><style>#pantalla-carga { display: none !important; }</style></noscript>
</head>
<body>

<div id="pantalla-carga" aria-hidden="true">
    <img src="assets/img/logo-512.png" alt="">
    <p>Cargando tu cuenta…</p>
    <div class="carga-barra"><span></span></div>
</div>

<header class="barra-superior">
    <a class="marca" href="<?= $enlaceMarca ?>">
        <img src="assets/img/logo-512.png" alt="" width="40" height="40">
        <span>Aurora</span>
    </a>

    <?php if (hay_sesion()): ?>
        <a class="boton-salir" href="logout.php">Salir</a>
    <?php endif; ?>
</header>
