<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registrar Pago</title>
</head>
<link rel="stylesheet" href="../css/estilos.css">
<body>
<form id="form1" name="form1" method="post" action="recibe_pago.php">
  <p align="center">REGISTRAR PAGO</p>
  <table width="347" border="1" align="center">
    <tr>
      <td width="150">Número de Factura</td>
      <td>
        <input type="text" name="numero_factura" required />
      </td>
    </tr>
    <tr>
      <td>Valor Pagado</td>
      <td>
        <input type="number" name="valor_pagado" min="0" step="0.01" required />
      </td>
    </tr>
  </table>
  <br />
  <div align="center">
    <input type="submit" name="Submit" value="Guardar" />
    <a href="index_productos.php">Regresar</a>
  </div>
</form>
</body>
</html>
