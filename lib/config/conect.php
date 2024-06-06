<?php
	$server="localhost";
	$user="root";
	$pass="";
	$bd="tesis";

	$con=mysqli_connect("$server","$user","$pass")or die ("Error al conectar con el Servidor");
	mysqli_select_db($con,"$bd");

	mysqli_query ($con,"SET NAMES 'utf8'");
	date_default_timezone_set('America/El_Salvador');
?>