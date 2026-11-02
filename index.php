<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Menú principal</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;800&display=swap">
<link rel="stylesheet" href="comun/css/menu.css">
</head>
<body>

<main>
    <h1>¿A qué apartado quieres entrar?</h1>
    <p class="subtitulo">Elige uno para continuar.</p>

    <div class="apartados">
        <a class="apartado" href="clientes/index.php">
            <h2>Clientes</h2>
            <p>Registrar, consultar, mostrar y actualizar clientes.</p>
            <span class="etiqueta">PHP y base de datos</span>
        </a>

        <a class="apartado" href="productos_factura/index_productos.php">
            <h2>Factura de productos</h2>
            <p>Facturas, productos y pagos de cada cliente.</p>
            <span class="etiqueta">PHP y base de datos</span>
        </a>

        <a class="apartado" href="tablas/index.html">
            <h2>Tablas</h2>
            <p>Página estilo periódico con columnas y transformaciones CSS.</p>
            <span class="etiqueta">Solo HTML y CSS</span>
        </a>

        <a class="apartado" href="banco/index.php">
            <h2>Banco</h2>
            <p>Ingresa con tu usuario para ver tu saldo y el historial de movimientos.</p>
            <span class="etiqueta">PHP y base de datos</span>
        </a>
    </div>
</main>

</body>
</html>
