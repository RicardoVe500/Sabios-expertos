<?php
/*  	session_start();
	session_unset();
	session_destroy();
	header("Location: ../../../../login.php"); */

session_start();

if(isset($_SESSION['logueado'])){ 
	session_destroy(); //Si existe una sesion iniciada y se cierra automaticamente se destruye
}
header("Location: ../../login.php"); //Autometicamente se dirige a login
exit();