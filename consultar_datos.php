<?php
// Validar que se haya enviado una cédula
if (!isset($_POST['cedula']) || empty(trim($_POST['cedula']))) {
    header('Location: ingresar_cedula.php');
    exit;
}

$cedula = trim($_POST['cedula']);

include_once("Cservicios.php");
$objCliente = new cCliente;
$datos = $objCliente->consultar_cliente($cedula);
$cliente = $datos['cliente'];
$pagos = $datos['pagos'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resultado de la Consulta</title>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<h2 align="center">Información del Cliente</h2>

<?php if ($cliente): ?>
    <table width="500" border="1" align="center">
        <tr>
            <th width="150" align="left">Cédula:</th>
            <td><?php echo htmlspecialchars($cliente['cedula']); ?></td>
        </tr>
        <tr>
            <th align="left">Nombres:</th>
            <td><?php echo htmlspecialchars($cliente['nombres']); ?></td>
        </tr>
        <tr>
            <th align="left">Apellidos:</th>
            <td><?php echo htmlspecialchars($cliente['apellidos']); ?></td>
        </tr>
        <tr>
            <th align="left">Dirección:</th>
            <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
        </tr>
        <tr>
            <th align="left">Email:</th>
            <td><?php echo htmlspecialchars($cliente['email']); ?></td>
        </tr>
        <tr>
            <th align="left">Celular:</th>
            <td><?php echo htmlspecialchars($cliente['celular']); ?></td>
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
        <?php if (count($pagos) > 0): ?>
            <?php foreach ($pagos as $pago): ?>
            <tr>
                <td align="center">$<?php echo htmlspecialchars($pago['valor_pagado']); ?></td>
                <td align="center"><?php echo htmlspecialchars($pago['fecha']); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2" align="center">No se encontraron pagos registrados.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

<?php else: ?>
    <p align="center" style="color: red;">No se encontró ningún cliente registrado con la cédula: <strong><?php echo htmlspecialchars($cedula); ?></strong></p>
<?php endif; ?>

<br />
<div align="center">
    <a href="ingresar_cedula.php">Nueva Consulta</a> |
    <a href="index.php">Regresar al Menú</a>
</div>

</body>
</html>
