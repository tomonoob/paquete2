<?php
class cCliente
{

	function registrar_cliente($cedula,$nombres,$apellidos,$direccion,$email,$celular)
	{
        include_once("conexion.php");
        $sql="CALL insertar_clientes7('$cedula','$nombres','$apellidos','$direccion','$email','$celular')";
        if($conexion->query($sql)==TRUE){
          echo "datos insertados correctamente";
        }
        else{
                echo "error al insertar los datos" . $conexion->error;

        }
        header('Location: index.php');exit;
	}
}
