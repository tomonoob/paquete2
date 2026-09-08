<?php
class cProducto
{
    function registrar_producto($numero_factura, $nombre_producto, $cantidad, $precio_unitario)
    {
        global $conexion;
        include_once("conexion.php");
        include_once(__DIR__ . "/ServiciosFactura.php");

        $objFactura = new cFactura;
        if (!$objFactura->obtener_factura($numero_factura)) {
            return "La factura '$numero_factura' no existe. Registrala primero.";
        }

        $r = llamar_procedimiento(
            $conexion,
            "CALL insertar_producto_factura(?,?,?,?)",
            [$numero_factura, $nombre_producto, $cantidad, $precio_unitario]
        );

        return $r->ok ? true : $r->error;
    }

    function mostrar_todos()
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL mostrar_todos_productos()");

        $productos = [];
        while ($row = $r->result->fetch_assoc()) {
            $productos[] = $row;
        }

        return $productos;
    }

    function consultar_por_factura($numero_factura)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL consultar_productos_por_factura(?)", [$numero_factura]);

        $productos = [];
        while ($row = $r->result->fetch_assoc()) {
            $productos[] = $row;
        }

        return $productos;
    }

    function obtener_producto($id)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento($conexion, "CALL mostrar_producto_por_id(?)", [$id]);

        return $r->result ? $r->result->fetch_assoc() : null;
    }

    function actualizar_producto($id, $numero_factura, $nombre_producto, $cantidad, $precio_unitario)
    {
        global $conexion;
        include_once("conexion.php");

        $r = llamar_procedimiento(
            $conexion,
            "CALL actualizar_producto_factura(?,?,?,?,?)",
            [$id, $numero_factura, $nombre_producto, $cantidad, $precio_unitario]
        );

        return $r->ok ? true : $r->error;
    }
}
