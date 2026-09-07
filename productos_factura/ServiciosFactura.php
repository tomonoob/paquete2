<?php
class cFactura
{
    function crear_factura($numero_factura, $cedula)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare("INSERT INTO facturas (numero_factura, cedula) VALUES (?, ?)");
        $stmt->bind_param("ss", $numero_factura, $cedula);
        $ok = $stmt->execute();
        $error = $stmt->error;

        $stmt->close();

        return $ok ? true : $error;
    }

    function obtener_factura($numero_factura)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare(
            "SELECT f.numero_factura, f.cedula, f.fecha, c.nombres, c.apellidos
             FROM facturas f
             LEFT JOIN clientes c ON c.cedula COLLATE utf8mb4_general_ci = f.cedula COLLATE utf8mb4_general_ci
             WHERE f.numero_factura = ?"
        );
        $stmt->bind_param("s", $numero_factura);
        $stmt->execute();
        $factura = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $factura ?: null;
    }

    function listar_por_cliente($cedula)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare(
            "SELECT f.numero_factura, f.fecha,
                    COALESCE(SUM(p.cantidad * p.precio_unitario), 0) AS total
             FROM facturas f
             LEFT JOIN productos_factura p ON p.numero_factura = f.numero_factura
             WHERE f.cedula COLLATE utf8mb4_general_ci = ? COLLATE utf8mb4_general_ci
             GROUP BY f.numero_factura, f.fecha
             ORDER BY f.fecha DESC"
        );
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();

        $facturas = [];
        while ($row = $result->fetch_assoc()) {
            $facturas[] = $row;
        }
        $stmt->close();

        return $facturas;
    }

    function registrar_pago($numero_factura, $valor_pagado)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare("INSERT INTO movimientos (numero_factura, valor_pagado) VALUES (?, ?)");
        $stmt->bind_param("sd", $numero_factura, $valor_pagado);
        $ok = $stmt->execute();
        $error = $stmt->error;

        $stmt->close();

        return $ok ? true : $error;
    }
}
