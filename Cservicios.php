<?php
class cCliente
{
    function registrar_cliente($cedula, $nombres, $apellidos, $direccion, $email, $celular)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare("CALL insertar_clientes7(?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $cedula, $nombres, $apellidos, $direccion, $email, $celular);
        $ok = $stmt->execute();
        $error = $stmt->error;

        $stmt->close();

        return $ok ? true : $error;
    }

    function consultar_cliente($cedula)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare("SELECT * FROM clientes WHERE cedula COLLATE utf8mb4_general_ci = ? COLLATE utf8mb4_general_ci");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $cliente = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $facturas = [];
        $pagos = [];
        if ($cliente) {
            include_once(__DIR__ . "/productos_factura/ServiciosFactura.php");
            $objFactura = new cFactura;
            $facturas = $objFactura->listar_por_cliente($cedula);

            $stmt = $conexion->prepare(
                "SELECT m.valor_pagado, m.fecha, m.numero_factura
                 FROM movimientos m
                 JOIN facturas f ON f.numero_factura = m.numero_factura
                 WHERE f.cedula COLLATE utf8mb4_general_ci = ? COLLATE utf8mb4_general_ci
                 ORDER BY m.fecha DESC"
            );
            $stmt->bind_param("s", $cedula);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $pagos[] = $row;
            }
            $stmt->close();
        }

        return ['cliente' => $cliente, 'facturas' => $facturas, 'pagos' => $pagos];
    }

    function actualizar_cliente($cedula, $nombres, $apellidos, $direccion, $email, $celular)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare("UPDATE clientes SET nombres = ?, apellidos = ?, direccion = ?, email = ?, celular = ? WHERE cedula = ?");
        $stmt->bind_param("ssssss", $nombres, $apellidos, $direccion, $email, $celular, $cedula);
        $ok = $stmt->execute();
        $error = $stmt->error;

        $stmt->close();

        return $ok ? true : $error;
    }

    function mostrar_todos()
    {
        global $conexion;
        include_once("conexion.php");

        $result = $conexion->query("SELECT * FROM clientes");

        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }

        return $clientes;
    }
}
