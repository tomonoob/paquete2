<?php
class cFactura
{
    function crear_factura($numero_factura, $cedula)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL crear_factura(?,?)", [$numero_factura, $cedula]);

        return $r->ok ? true : $r->error;
    }

    function obtener_factura($numero_factura)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL obtener_factura(?)", [$numero_factura]);
        $factura = $r->result ? $r->result->fetch_assoc() : null;

        return $factura ?: null;
    }

    function listar_por_cliente($cedula)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL listar_facturas_cliente(?)", [$cedula]);

        $facturas = [];
        while ($row = $r->result->fetch_assoc()) {
            $facturas[] = $row;
        }

        return $facturas;
    }

    function registrar_pago($numero_factura, $valor_pagado)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL registrar_pago(?,?)", [$numero_factura, $valor_pagado]);

        return $r->ok ? true : $r->error;
    }
}
