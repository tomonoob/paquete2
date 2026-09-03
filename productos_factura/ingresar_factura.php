<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Consultar Productos de una Factura</title>
<link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<form id="form1" name="form1" method="post" action="consultar_productos.php">
  <p align="center">CONSULTAR PRODUCTOS DE UNA FACTURA</p>
  <table width="347" border="1" align="center">
    <tr>
      <td width="150">Número de Factura</td>
      <td>
        <input type="text" name="numero_factura" required />
      </td>
    </tr>
  </table>
  <br />
  <div align="center">
    <input type="submit" name="Submit" value="Buscar" />
    <a href="index_productos.php">Regresar</a>
  </div>
</form>
</body>
</html>
