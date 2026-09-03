<?php
$cedula=$_POST['cedula'];
$nombres=$_POST['nombres'];
$apellidos=$_POST['apellidos'];
$direccion=$_POST['direccion'];
$email=$_POST['email'];
$celular=$_POST['celular'];

include_once("Cservicios.php");

$objconsulta=new cCliente;
$result=$objconsulta->registrar_cliente($cedula,$nombres,$apellidos,$direccion,$email,$celular);


?>