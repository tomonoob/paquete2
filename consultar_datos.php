<?php
// Validar que se haya enviado una cédula
if (!isset($_POST['cedula']) || empty($_POST['cedula'])) {
    header('Location: ingresar_cedula.php');
    exit;
}

$cedula = $_POST['cedula'];

// Conexión a la base de datos
include_once("conexion.php");

$sql_cliente = "SELECT * FROM clientes WHERE cedula COLLATE utf8mb4_general_ci = '$cedula' COLLATE utf8mb4_general_ci";
$res_cliente = $conexion->query($sql_cliente);
$cliente = $res_cliente ? $res_cliente->fetch_assoc() : null;

$sql_pagos = "SELECT valor_pagado, fecha FROM movimientos WHERE cedula COLLATE utf8mb4_general_ci = '$cedula' COLLATE utf8mb4_general_ci";
$res_pagos = $conexion->query($sql_pagos);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Resultado de la Consulta</title>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<h2 align="center">Información del Cliente</h2>

<?php if ($cliente): ?>
    <table width="500" border="1" align="center">
        <tr>
            <th width="150" align="left">Cédula:</th>
            <td><?php echo $cliente['cedula']; ?></td>
        </tr>
        <tr>
            <th align="left">Nombres:</th>
            <td><?php echo $cliente['nombres']; ?></td>
        </tr>
        <tr>
            <th align="left">Apellidos:</th>
            <td><?php echo $cliente['apellidos']; ?></td>
        </tr>
        <tr>
            <th align="left">Dirección:</th>
            <td><?php echo $cliente['direccion']; ?></td>
        </tr>
        <tr>
            <th align="left">Email:</th>
            <td><?php echo $cliente['email']; ?></td>
        </tr>
        <tr>
            <th align="left">Celular:</th>
            <td><?php echo $cliente['celular']; ?></td>
        </tr>
    </table>

    <br />
    <h3 align="center">Historial de Pagos</h3>

    <table width="500" border="1" align="center">
        <thead>
            <tr>
                <th>Valor Pagado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        if ($res_pagos && $res_pagos->num_rows > 0) {
            while ($pago = $res_pagos->fetch_assoc()) { 
        ?>
            <tr>
                <td align="center">$<?php echo $pago['valor_pagado']; ?></td>
                <td align="center"><?php echo $pago['fecha']; ?></td>
            </tr>
        <?php 
            } 
        } else { 
        ?>
            <tr>
                <td colspan="2" align="center">No se encontraron pagos registrados.</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<?php else: ?>
    <p align="center" style="color: red;">No se encontró ningún cliente registrado con la cédula: <strong><?php echo $cedula; ?></strong></p>
<?php endif; ?>

<br />
<div align="center">
    <a href="ingresar_cedula.php">Nueva Consulta</a> | 
    <a href="index.php">Regresar al Menú</a>
</div>

</body>
</html>