<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Actualizar Producto</title>
<link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<form id="form1" name="form1" method="post" action="actualizarproducto.php">
  <p align="center">ACTUALIZAR PRODUCTO</p>
  <table width="347" border="1" align="center">
    <tr>
      <td width="150">ID del Producto</td>
      <td>
        <input type="text" name="id" required />
      </td>
    </tr>
  </table>
  <p align="center"><small>El ID se muestra en "Consultar" o "Mostrar Todo".</small></p>
  <br />
  <div align="center">
    <input type="submit" name="Submit" value="Cargar Datos" />
    <a href="index_productos.php">Regresar</a>
  </div>
</form>
</body>
</html>
