<?php
include_once("Cservicios.php");

$cedula    = trim($_POST['cedula'] ?? '');
$nombres   = trim($_POST['nombres'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$email     = trim($_POST['email'] ?? '');
$celular   = trim($_POST['celular'] ?? '');

$objCliente = new cCliente;
$resultado = $objCliente->registrar_cliente($cedula, $nombres, $apellidos, $direccion, $email, $celular);

if ($resultado === true) {
    echo "<script>
            alert('¡Datos insertados correctamente!');
            window.location.href = 'index.php';
          </script>";
} else {
    echo "Error al insertar los datos: " . htmlspecialchars($resultado);
}
