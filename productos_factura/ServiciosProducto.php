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

        $stmt = $conexion->prepare(
            "INSERT INTO productos_factura (numero_factura, nombre_producto, cantidad, precio_unitario)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssid", $numero_factura, $nombre_producto, $cantidad, $precio_unitario);
        $ok = $stmt->execute();
        $error = $stmt->error;

        $stmt->close();

        return $ok ? true : $error;
    }

    function mostrar_todos()
    {
        global $conexion;
        include_once("conexion.php");

        $result = $conexion->query("SELECT * FROM productos_factura ORDER BY id");

        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }

        return $productos;
    }

    function consultar_por_factura($numero_factura)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare(
            "SELECT * FROM productos_factura WHERE numero_factura = ? ORDER BY id"
        );
        $stmt->bind_param("s", $numero_factura);
        $stmt->execute();
        $result = $stmt->get_result();

        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }

        $stmt->close();
        return $productos;
    }

    function actualizar_producto($id, $numero_factura, $nombre_producto, $cantidad, $precio_unitario)
    {
        global $conexion;
        include_once("conexion.php");

        $stmt = $conexion->prepare(
            "UPDATE productos_factura
             SET numero_factura = ?, nombre_producto = ?, cantidad = ?, precio_unitario = ?
             WHERE id = ?"
        );
        $stmt->bind_param("ssidi", $numero_factura, $nombre_producto, $cantidad, $precio_unitario, $id);
        $ok = $stmt->execute();
        $error = $conexion->error;

        $stmt->close();

        return $ok ? true : $error;
    }
}
