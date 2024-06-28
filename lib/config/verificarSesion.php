<?php

//Iniciar Sesion
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['logueado']) or $_SESSION['logueado'] =="") {
    // Redirigir al usuario a la página de login si no está logueado
    header("Location: ../../../../login.php"); 
    /* exit(); */
}

// Verificar el rol del usuario
/* if (!isset($_SESSION['nombreTipo']) || $_SESSION['nombreTipo'] !== 'Administrador') {
    die('Acceso denegado: no tienes permiso para acceder a esta página.');
}  */

