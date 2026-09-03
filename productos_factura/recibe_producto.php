<?php

$numero_factura  = trim($_POST['numero_factura']);
$nombre_producto = trim($_POST['nombre_producto']);
$cantidad        = (int)$_POST['cantidad'];
$precio_unitario = (float)$_POST['precio_unitario'];

include_once("ServiciosProducto.php");
$objProducto = new cProducto;
$objProducto->registrar_producto($numero_factura, $nombre_producto, $cantidad, $precio_unitario);

?>
