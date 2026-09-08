<?php
class cCliente
{
    function registrar_cliente($cedula, $nombres, $apellidos, $direccion, $email, $celular)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento(
            $conexion,
            "CALL insertar_clientes7(?,?,?,?,?,?)",
            [$cedula, $nombres, $apellidos, $direccion, $email, $celular]
        );

        return $r->ok ? true : $r->error;
    }

    function consultar_cliente($cedula)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL mostrar_para_actualizar(?)", [$cedula]);
        $cliente = $r->result ? $r->result->fetch_assoc() : null;

        $facturas = [];
        $pagos = [];
        if ($cliente) {
            include_once(__DIR__ . "/productos_factura/ServiciosFactura.php");
            $objFactura = new cFactura;
            $facturas = $objFactura->listar_por_cliente($cedula);

            $r = llamar_procedimiento($conexion, "CALL mostrar_pagos(?)", [$cedula]);
            while ($row = $r->result->fetch_assoc()) {
                $pagos[] = $row;
            }
        }

        return ['cliente' => $cliente, 'facturas' => $facturas, 'pagos' => $pagos];
    }

    function actualizar_cliente($cedula, $nombres, $apellidos, $direccion, $email, $celular)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento(
            $conexion,
            "CALL actualizar_uncliente(?,?,?,?,?,?)",
            [$cedula, $nombres, $apellidos, $direccion, $email, $celular]
        );

        return $r->ok ? true : $r->error;
    }

    function mostrar_todos()
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL mostrar_todos_clientes()");

        $clientes = [];
        while ($row = $r->result->fetch_assoc()) {
            $clientes[] = $row;
        }

        return $clientes;
    }
}
