<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ing. Antonio Borré B.</title>
</head>
<link rel="stylesheet" href="css/estilos.css">
<body>
<form id="form1" name="form1" method="post" action="recibe_datos.php">
  <p align="center">REGISTRAR DATOS </p>
  <table width="347" border="1" align="center">
  <tr>
    <td width="89">Cedula</td>
    <td width="242">
      <label>
        <input type="text"  name="cedula"  value=""/>
      </label>
    </td>
  </tr>
  <tr>
    <td>Nombres</td>
    <td>
      <label>
        <input name="nombres" type="text" id="nombres"  />
      </label></td>
  </tr>
  <tr>
    <td>Apellidos</td>
	<td>
	 <label>
        <input type="text"  name="apellidos" />
      </label>    </td>
  </tr>
  <tr>
    <td>Direccion</td>
    <td>
      <label>
        <input type="text"  name="direccion" />
      </label>    </td>
  </tr>

  <tr>
    <td>Email</td>
    <td>
      <label>
        <input type="text"  name="email" />
      </label>    </td>
  </tr>
  <tr>
    <td>Celular</td>
    <td>
      <label>
        <input type="text"  name="celular" />
      </label>    </td>
  </tr>
</table>
  <label>
  <div align="center">
    <input type="submit" name="Submit" value="Guardar" />
    <a href="index.php">Regresar</a></div>
</form>
<p align="center">&nbsp;</p>
</body>
</html>
