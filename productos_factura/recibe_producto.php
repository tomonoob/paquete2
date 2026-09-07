<?php
include_once("ServiciosProducto.php");

$numero_factura  = trim($_POST['numero_factura'] ?? '');
$nombre_producto = trim($_POST['nombre_producto'] ?? '');
$cantidad        = (int)($_POST['cantidad'] ?? 0);
$precio_unitario = (float)($_POST['precio_unitario'] ?? 0);

$objProducto = new cProducto;
$resultado = $objProducto->registrar_producto($numero_factura, $nombre_producto, $cantidad, $precio_unitario);

if ($resultado === true) {
    echo "<script>
            alert('¡Producto registrado correctamente!');
            window.location.href = 'index_productos.php';
          </script>";
} else {
    echo "Error al insertar el producto: " . htmlspecialchars($resultado);
}
