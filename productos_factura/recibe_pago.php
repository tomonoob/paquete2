<?php
include_once("ServiciosFactura.php");

$numero_factura = trim($_POST['numero_factura'] ?? '');
$valor_pagado   = (float)($_POST['valor_pagado'] ?? 0);

$objFactura = new cFactura;

if (!$objFactura->obtener_factura($numero_factura)) {
    echo "Error: la factura '" . htmlspecialchars($numero_factura) . "' no existe.";
    exit;
}

$resultado = $objFactura->registrar_pago($numero_factura, $valor_pagado);

if ($resultado === true) {
    echo "<script>
            alert('¡Pago registrado correctamente!');
            window.location.href = 'index_productos.php';
          </script>";
} else {
    echo "Error al registrar el pago: " . htmlspecialchars($resultado);
}
