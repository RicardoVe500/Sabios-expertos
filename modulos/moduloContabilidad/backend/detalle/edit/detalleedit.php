<?php
include("../../../../../lib/config/conect.php");

$usuario_sesion = $_SESSION['usuario'];


$cuentaId = $_POST["cuentaId"];
$partidaId = $_POST["partidaId"];
$fechacontable = $_POST["fechacontable"];
$cargo = $_POST["cargo"] ?? 0;
$abono = $_POST["abono"] ?? 0;



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Suponiendo que $con ya está establecido y es la conexión a tu base de datos.

$queryInsert = "UPDATE detalle SET fechacontable = '$fechacontable', haber = '$cargo', debe = '$abono'
                WHERE partidaId = '$partidaId'";

$resultInsert = mysqli_query($con, $queryInsert);

if ($resultInsert) {
    
    echo json_encode(['status' => 'success', 'message' => 'Actualizacion realizada correctamente']);
} else {
   
    $errorMessage = mysqli_error($con);

    echo json_encode(['status' => 'success', 'message' => $errorMessage]);

}
?>
