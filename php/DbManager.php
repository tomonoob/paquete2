<?php
//Clase Manejadora de la base de datos y conexión con el servidor
class DBManager
{
	var $conect;
	
	//Constructor
	function DBManager()
	{
	}
	
	//Función conectar con el servidor y la base de datos
	function conectar()
	{
		$con=@mysql_connect("127.0.0.1","root","root");
                if ($con==false)
		{
			echo "Error al conextarse con el servidor1";
			exit();
		}
                else{
                       //echo "Hubo conexion con el servidor";
                     }
                 $result=@mysql_select_db("bd_ejemplo2",$con);
		if ( $result==false)
		{
			echo "Error al seleccionar la base de datos";
			exit();
		}
                else{
                       //echo "Registrado Correctamente";
                     }
		$this->conect=$con;
		return true;
	}
				
}
?>