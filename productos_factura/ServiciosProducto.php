<?php
class cProducto
{
    // Inserta un nuevo producto asociado a un número de factura
    function registrar_producto($numero_factura, $nombre_producto, $cantidad, $precio_unitario)
    {
        include_once("conexion.php");

        $stmt = $conexion->prepare(
            "INSERT INTO productos_factura (numero_factura, nombre_producto, cantidad, precio_unitario)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssid", $numero_factura, $nombre_producto, $cantidad, $precio_unitario);

        if ($stmt->execute()) {
            echo "<script>
                    alert('¡Producto registrado correctamente!');
                    window.location.href = 'index_productos.php';
                  </script>";
        } else {
            echo "Error al insertar el producto: " . $conexion->error;
        }

        $stmt->close();
        $conexion->close();
    }

    // Devuelve todos los productos de una factura (array asociativo)
    function consultar_por_factura($numero_factura)
    {
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
        $conexion->close();
        return $productos;
    }

    // Actualiza un producto existente identificado por su id
    function actualizar_producto($id, $numero_factura, $nombre_producto, $cantidad, $precio_unitario)
    {
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
        $conexion->close();

        return $ok ? true : $error;
    }
}
