<?php
// Validar que se haya enviado un número de factura
if (!isset($_POST['numero_factura']) || empty($_POST['numero_factura'])) {
    header('Location: ingresar_factura.php');
    exit;
}

$numero_factura = trim($_POST['numero_factura']);

include_once("ServiciosProducto.php");
$objProducto = new cProducto;
$productos = $objProducto->consultar_por_factura($numero_factura);

$total_factura = 0;
foreach ($productos as &$p) {
    $p['subtotal'] = $p['cantidad'] * $p['precio_unitario'];
    $total_factura += $p['subtotal'];
}
unset($p);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Productos de la Factura</title>
<link rel="stylesheet" href="../css/estilos.css">
</head>
<body>

<h2 align="center">Productos de la Factura N.° <?php echo htmlspecialchars($numero_factura); ?></h2>

<?php if (count($productos) > 0): ?>
    <table width="700" border="1" align="center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td align="center"><?php echo $p['id']; ?></td>
                <td><?php echo htmlspecialchars($p['nombre_producto']); ?></td>
                <td align="center"><?php echo $p['cantidad']; ?></td>
                <td align="right">$<?php echo number_format($p['precio_unitario'], 2); ?></td>
                <td align="right">$<?php echo number_format($p['subtotal'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
            <tr>
                <td colspan="4" align="right"><strong>Total Factura:</strong></td>
                <td align="right"><strong>$<?php echo number_format($total_factura, 2); ?></strong></td>
            </tr>
        </tbody>
    </table>
<?php else: ?>
    <p align="center" style="color: red;">No se encontraron productos para la factura: <strong><?php echo htmlspecialchars($numero_factura); ?></strong></p>
<?php endif; ?>

<br />
<div align="center">
    <a href="ingresar_factura.php">Nueva Consulta</a> |
    <a href="index_productos.php">Regresar al Menú</a>
</div>

</body>
</html>
