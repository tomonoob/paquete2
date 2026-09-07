<?php
class cCliente
{

	function registrar_cliente($cedula,$nombres,$apellidos,$direccion,$email,$celular)
	{
        include_once("conexion.php");
        $stmt = $conexion->prepare("CALL insertar_clientes7(?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $cedula, $nombres, $apellidos, $direccion, $email, $celular);
        if($stmt->execute()){
          echo "<script>
                    alert('¡Datos insertados correctamente!');
                    window.location.href = 'index.php';
                  </script>";
        }
        else{
                echo "error al insertar los datos" . htmlspecialchars($stmt->error);

        }
        $stmt->close();
	}
}
