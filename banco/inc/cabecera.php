<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= limpiar($titulo) ?> · Cuenta</title>
<link rel="icon" href="assets/img/logo.png">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;800&display=swap">
<link rel="stylesheet" href="assets/css/estilos.css">
<script>
var tipoCarga = performance.getEntriesByType('navigation')[0].type;
var vieneDeBanco = document.referrer.indexOf('/banco/') != -1;
if (vieneDeBanco || tipoCarga == 'reload' || tipoCarga == 'back_forward') {
    document.documentElement.classList.add('sin-loader');
}
</script>
</head>
<body>

<header>
    <a id="destino-logo" href="index.php"></a>
    <span class="marca">Cuenta</span>

    <div id="usuario-menu">
        <?php if (isset($_SESSION['modo'])): ?>
            <span><?= limpiar($_SESSION['nombre_completo']) ?></span>
        <?php endif; ?>
        <a class="boton-borde" href="../index.php">Menú</a>
        <?php if (isset($_SESSION['modo'])): ?>
            <a class="boton-borde" href="logout.php">Salir</a>
        <?php endif; ?>
    </div>
</header>
