
 <?php

include_once("DbManager.php");

class cCliente
{
	//Constructor
	function cCliente()
	{
	
	   
	}
	
	//Funcion consultar todos los datos
	function consultar_cliente($cedula)
	{
		
		$con=new DBManager;
		
		//usamos el mtodo conectar para realizar la conexin
		if ($con->conectar()==true)
		{
			$query="SELECT * from tclientes where cedula='$cedula'";
			$resultado=mysql_query($query) or die("Error en Consulta de Titulares");
			return $resultado;
		}
		
	}
	// generar informe
	
	function Mostrar_todo()
	{
		
		$con=new DBManager;
		
		//usamos el mtodo conectar para realizar la conexin
		if ($con->conectar()==true)
		{
			$query="SELECT * from tclientes";
			$resultado=mysql_query($query) or die("Error en Consulta de Titulares");
			return $resultado;
		}
		
	}
	
	function actualizar_cliente($cedula,$nombres,$apellidos,$direccion,$email,$celular)
	{
		//creamos el objeto $con a partir de la clase DBManager
		$con=new DBManager;
		
		//usamos el mtodo conectar para realizar la conexin
		if ($con->conectar()==true)
		{
			$query="update tclientes set nombres='$nombres',apellidos='$apellidos',direccion='$direccion',email='$email',celular='$celular' where cedula='$cedula'";
			$resultado=mysql_query($query) or die("Error en Consulta de Titulares");
			
		}
		?>
		
 <table width="324" height="29" border="1" align="center">      
  <tr>
    <td width="291" height="23" colspan="2" valign="top"><label>
      LOS DATOS SE ACTUALIZARON        </label></td>
    </tr>
</table>
		<?php
		
		 include_once("ingresar_cedula2.php");
	}
	function registrar_cliente($cedula,$nombres,$apellidos,$direccion,$email,$celular)
	{
		//creamos el objeto $con a partir de la clase DBManager
		$con=new DBManager;
		if ($con->conectar()==true)
		{
			$query="insert into tclientes(cedula,nombres,apellidos,direccion,email,celular) VALUES('$cedula','$nombres','$apellidos','$direccion','$email','$celular')";
			$resultado=mysql_query($query) or die("Error en registro cliente");
		}
                 include_once("registrardatos.php");
	}
	
		
	//buscar las atenciones
	
	
}



?>
</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
