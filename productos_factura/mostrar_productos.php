<?php
include_once("conexion.php");

$sql = "SELECT * FROM productos_factura ORDER BY numero_factura, id";
$result = $conexion->query($sql);

$total_general = 0;
$filas = [];
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['cantidad'] * $row['precio_unitario'];
    $total_general += $row['subtotal'];
    $filas[] = $row;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Mostrar Todos los Productos</title>
<link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <h1 align="center">Informe de Productos de Todas las Facturas</h1>
    <table width="905" border="1" align="center">
        <thead>
            <tr>
                <th>ID</th>
                <th>N.° Factura</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($filas) > 0): ?>
            <?php foreach ($filas as $row): ?>
            <tr>
                <td align="center"><?php echo $row['id']; ?></td>
                <td align="center"><?php echo htmlspecialchars($row['numero_factura']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
                <td align="center"><?php echo $row['cantidad']; ?></td>
                <td align="right">$<?php echo number_format($row['precio_unitario'], 2); ?></td>
                <td align="right">$<?php echo number_format($row['subtotal'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="5" align="right"><strong>Total General:</strong></td>
                <td align="right"><strong>$<?php echo number_format($total_general, 2); ?></strong></td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="6" align="center">No hay productos registrados.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <br />
    <div align="center">
        <a href="index_productos.php">Regresar al Menú</a>
    </div>
</body>
</html>
