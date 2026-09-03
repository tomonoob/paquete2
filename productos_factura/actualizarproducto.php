<?php
include_once("ServiciosProducto.php");
include_once("conexion.php");

$mensaje = "";
$producto = null;

// 1. SI SE ENVIÓ EL FORMULARIO DE EDICIÓN (Guardar cambios)
if (isset($_POST['btn_guardar'])) {
    $id              = (int)$_POST['id'];
    $numero_factura  = trim($_POST['numero_factura']);
    $nombre_producto = trim($_POST['nombre_producto']);
    $cantidad        = (int)$_POST['cantidad'];
    $precio_unitario = (float)$_POST['precio_unitario'];

    $objProducto = new cProducto;
    $resultado = $objProducto->actualizar_producto($id, $numero_factura, $nombre_producto, $cantidad, $precio_unitario);

    if ($resultado === true) {
        $mensaje = "<p style='color: green; text-align: center;'><strong>¡Producto actualizado correctamente!</strong></p>";
    } else {
        $mensaje = "<p style='color: red; text-align: center;'>Error al actualizar: " . htmlspecialchars($resultado) . "</p>";
    }

    // Volver a cargar el producto actualizado para mostrarlo en el formulario
    $stmt = $conexion->prepare("SELECT * FROM productos_factura WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
// 2. SI SE LLEGA DESDE `ingresar_id_producto.php` (Buscar producto a editar)
else if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $conexion->prepare("SELECT * FROM productos_factura WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Actualizar Producto</title>
<link rel="stylesheet" href="../css/estilos.css">
</head>
<body>

<p align="center"><strong>ACTUALIZAR PRODUCTO</strong></p>

<?php echo $mensaje; ?>

<?php if ($producto): ?>
<form id="form1" name="form1" method="post" action="actualizarproducto.php">
  <table width="347" border="1" align="center">
    <tr>
      <td width="150">ID</td>
      <td width="242">
        <!-- El ID no se edita por ser la clave del registro -->
        <input type="text" name="id" value="<?php echo $producto['id']; ?>" readonly style="background-color: #e9e9e9;" />
      </td>
    </tr>
    <tr>
      <td>Número de Factura</td>
      <td>
        <input type="text" name="numero_factura" value="<?php echo htmlspecialchars($producto['numero_factura']); ?>" required />
      </td>
    </tr>
    <tr>
      <td>Nombre del Producto</td>
      <td>
        <input type="text" name="nombre_producto" value="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" required />
      </td>
    </tr>
    <tr>
      <td>Cantidad</td>
      <td>
        <input type="number" name="cantidad" min="1" step="1" value="<?php echo $producto['cantidad']; ?>" required />
      </td>
    </tr>
    <tr>
      <td>Precio Unitario</td>
      <td>
        <input type="number" name="precio_unitario" min="0" step="0.01" value="<?php echo $producto['precio_unitario']; ?>" required />
      </td>
    </tr>
  </table>
  <br />
  <div align="center">
    <input type="submit" name="btn_guardar" value="Actualizar" />
    <a href="index_productos.php">Regresar</a>
  </div>
</form>
<?php else: ?>
  <p align="center" style="color: red;">No se encontró ningún producto con ese ID.</p>
  <div align="center">
    <a href="ingresar_id_producto.php">Intentar de nuevo</a> |
    <a href="index_productos.php">Regresar al Menú</a>
  </div>
<?php endif; ?>

</body>
</html>
