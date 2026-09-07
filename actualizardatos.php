<?php
include_once("Cservicios.php");

$mensaje = "";
$cliente = null;
$objCliente = new cCliente;

// 1. SI SE ENVIÓ EL FORMULARIO DE EDICIÓN (Guardar cambios)
if (isset($_POST['btn_guardar'])) {
    $cedula    = trim($_POST['cedula']);
    $nombres   = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $direccion = trim($_POST['direccion']);
    $email     = trim($_POST['email']);
    $celular   = trim($_POST['celular']);

    $resultado = $objCliente->actualizar_cliente($cedula, $nombres, $apellidos, $direccion, $email, $celular);

    if ($resultado === true) {
        $mensaje = "<p style='color: green; text-align: center;'><strong>¡Datos actualizados correctamente!</strong></p>";
    } else {
        $mensaje = "<p style='color: red; text-align: center;'>Error al actualizar: " . htmlspecialchars($resultado) . "</p>";
    }

    // Volver a cargar los datos actualizados para mostrarlos en el formulario
    $cliente = $objCliente->consultar_cliente($cedula)['cliente'];
}
// 2. SI SE LLEGA DESDE `ingresar_cedula2.php` (Buscar cliente a editar)
else if (isset($_POST['cedula']) && !empty($_POST['cedula'])) {
    $cedula = trim($_POST['cedula']);
    $cliente = $objCliente->consultar_cliente($cedula)['cliente'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Actualizar Datos del Cliente</title>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<p align="center"><strong>ACTUALIZAR DATOS DE CLIENTE</strong></p>

<?php echo $mensaje; ?>

<?php if ($cliente): ?>
<form id="form1" name="form1" method="post" action="actualizardatos.php">
  <table width="347" border="1" align="center">
    <tr>
      <td width="89">Cédula</td>
      <td width="242">
        <!-- La cédula no se edita por ser la clave del registro -->
        <input type="text" name="cedula" value="<?php echo htmlspecialchars($cliente['cedula']); ?>" readonly style="background-color: #e9e9e9;" />
      </td>
    </tr>
    <tr>
      <td>Nombres</td>
      <td>
        <input name="nombres" type="text" value="<?php echo htmlspecialchars($cliente['nombres']); ?>" required />
      </td>
    </tr>
    <tr>
      <td>Apellidos</td>
      <td>
        <input type="text" name="apellidos" value="<?php echo htmlspecialchars($cliente['apellidos']); ?>" required />
      </td>
    </tr>
    <tr>
      <td>Dirección</td>
      <td>
        <input type="text" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion']); ?>" />
      </td>
    </tr>
    <tr>
      <td>Email</td>
      <td>
        <input type="text" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" />
      </td>
    </tr>
    <tr>
      <td>Celular</td>
      <td>
        <input type="text" name="celular" value="<?php echo htmlspecialchars($cliente['celular']); ?>" />
      </td>
    </tr>
  </table>
  <br />
  <div align="center">
    <input type="submit" name="btn_guardar" value="Actualizar" />
    <a href="index.php">Regresar</a>
  </div>
</form>
<?php else: ?>
  <p align="center" style="color: red;">No se encontró ningún cliente para editar.</p>
  <div align="center">
    <a href="ingresar_cedula2.php">Intentar de nuevo</a> |
    <a href="index.php">Regresar al Menú</a>
  </div>
<?php endif; ?>

</body>
</html>
