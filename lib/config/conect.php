<?php
//Conexion
	$server="localhost";
	$user="root";
	$pass="";
	$bd="tesis";

	$con = mysqli_connect("$server","$user","$pass", "$bd")or die ("Error al conectar con el Servidor");
	mysqli_select_db($con,"$bd");

	mysqli_query ($con,"SET NAMES 'utf8'");
	date_default_timezone_set('America/El_Salvador');


//Iniciar la sesión
if(!isset($_SESSION)){
	session_start();
}
	