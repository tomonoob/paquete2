<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registrar Factura</title>
</head>
<link rel="stylesheet" href="../css/estilos.css">
<body>
<form id="form1" name="form1" method="post" action="recibe_factura.php">
  <p align="center">REGISTRAR FACTURA</p>
  <table width="347" border="1" align="center">
    <tr>
      <td width="150">Cédula del Cliente</td>
      <td>
        <input type="text" name="cedula" required />
      </td>
    </tr>
    <tr>
      <td>Número de Factura</td>
      <td>
        <input type="text" name="numero_factura" required />
      </td>
    </tr>
  </table>
  <p align="center"><small>El cliente debe existir. Después de crear la factura vas a poder agregarle productos.</small></p>
  <br />
  <div align="center">
    <input type="submit" name="Submit" value="Guardar" />
    <a href="index_productos.php">Regresar</a>
  </div>
</form>
</body>
</html>
