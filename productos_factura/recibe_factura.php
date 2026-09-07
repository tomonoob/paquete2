<?php
include_once("ServiciosFactura.php");

$cedula         = trim($_POST['cedula'] ?? '');
$numero_factura = trim($_POST['numero_factura'] ?? '');

if ($cedula === '' || $numero_factura === '') {
    echo "Error: la cédula y el número de factura son obligatorios.";
    exit;
}

$objFactura = new cFactura;
$resultado = $objFactura->crear_factura($numero_factura, $cedula);

if ($resultado === true) {
    echo "<script>
            alert('¡Factura registrada correctamente!');
            window.location.href = 'registrarproducto.php';
          </script>";
} else {
    echo "Error al registrar la factura: " . htmlspecialchars($resultado);
}
