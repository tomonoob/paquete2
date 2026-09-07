<?php
include_once("Cservicios.php");

$objCliente = new cCliente;
$clientes = $objCliente->mostrar_todos();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mostrar Todos los Clientes</title>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <h1 align="center">Informe de Datos Colectivos</h1>
    <table width="905" border="1" align="center">
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Dirección</th>
                <th>Email</th>
                <th>Celular</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($clientes as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                <td><?php echo htmlspecialchars($row['nombres']); ?></td>
                <td><?php echo htmlspecialchars($row['apellidos']); ?></td>
                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['celular']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br />
    <div align="center">
        <a href="index.php">Regresar al Menú</a>
    </div>
</body>
</html>
