<?php
class cCliente
{

	function registrar_cliente($cedula,$nombres,$apellidos,$direccion,$email,$celular)
	{
        include_once("conexion.php");
        $sql="CALL insertar_clientes7('$cedula','$nombres','$apellidos','$direccion','$email','$celular')";
        if($conexion->query($sql)==TRUE){
          echo "<script>
                    alert('¡Datos insertados correctamente!');
                    window.location.href = 'index.php';
                  </script>";
        }
        else{
                echo "error al insertar los datos" . $conexion->error;

        }
	}
}
