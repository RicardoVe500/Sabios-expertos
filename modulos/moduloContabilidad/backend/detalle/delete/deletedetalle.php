<?php
include("../../../../../lib/config/conect.php");
$usuario_sesion = $_SESSION['usuario'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



if (isset($_POST['partidaId'])|| isset($_POST['cuentaId'])) {

    $partidaId = $_POST['partidaId'];
    $cuentaId = $_POST['cuentaId'];
    $fechaHoraActual = date("Y-m-d H:i:s");

    $query = "DELETE FROM detalle WHERE partidaId = $partidaId AND cuentaId = $cuentaId";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die ("Error en la consulta".mysqli_error($con));
    }
    else{
        echo "Todo bien";
    }
}
