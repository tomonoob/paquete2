<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Registrar Producto</title>
</head>
<link rel="stylesheet" href="../css/estilos.css">
<body>
<form id="form1" name="form1" method="post" action="recibe_producto.php">
  <p align="center">REGISTRAR PRODUCTO EN UNA FACTURA</p>
  <table width="347" border="1" align="center">
    <tr>
      <td width="150">Número de Factura</td>
      <td>
        <label>
          <input type="text" name="numero_factura" required />
        </label>
      </td>
    </tr>
    <tr>
      <td>Nombre del Producto</td>
      <td>
        <label>
          <input type="text" name="nombre_producto" id="nombre_producto" required />
        </label>
      </td>
    </tr>
    <tr>
      <td>Cantidad</td>
      <td>
        <label>
          <input type="number" name="cantidad" min="1" step="1" required />
        </label>
      </td>
    </tr>
    <tr>
      <td>Precio Unitario</td>
      <td>
        <label>
          <input type="number" name="precio_unitario" min="0" step="0.01" required />
        </label>
      </td>
    </tr>
  </table>
  <label>
  <div align="center">
    <input type="submit" name="Submit" value="Guardar" />
    <a href="index_productos.php">Regresar</a></div>
  </label>
</form>
<p align="center">&nbsp;</p>
</body>
</html>
