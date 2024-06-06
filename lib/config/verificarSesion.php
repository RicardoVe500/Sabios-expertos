<?php 

    session_start();

    if (!isset($_SESSION["logueado"]) or $_SESSION["logueado"]==""){
        header("Location: ../../login.php");
    }

?>